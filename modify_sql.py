import re
import json
from datetime import datetime

with open('database_export.sql', 'r', encoding='utf-8') as f:
    sql = f.read()

# We need to replace the inserts for service_id = 1
# service_requirements has columns: id, service_id, requirement_text, is_required, display_order, created_at, updated_at

# The new requirements for Educational Assistance (service_id = 1)
new_reqs = [
    'Certificate of (Enrollment, Grade, Registration)',
    'Statement of account',
    'School ID',
    'Letter request',
    'Endorsement'
]

# We need to find the INSERT INTO `service_requirements` block
match = re.search(r'INSERT INTO `service_requirements` VALUES\n(.*?);', sql, re.DOTALL)
if match:
    values_str = match.group(1)
    
    # parse the tuples
    tuples = []
    # simplistic parsing: split by "),\n("
    raw_tuples = values_str.strip().strip("()").split("),\n(")
    
    # filter out service_id = 1
    other_tuples = [t for t in raw_tuples if not t.startswith('1,') and not t.startswith('2,1,') and not t.startswith('3,1,')]
    
    # wait, the other tuples have ids 4-13.
    # to be safe, let's keep all tuples where the second field (service_id) is NOT 1.
    def get_service_id(t):
        parts = t.split(',')
        return parts[1].strip()
        
    kept_tuples = [t for t in raw_tuples if get_service_id(t) != '1']
    
    # find the highest id used in the table
    max_id = 0
    for t in raw_tuples:
        curr_id = int(t.split(',')[0].strip())
        if curr_id > max_id:
            max_id = curr_id
            
    # create new tuples for service_id = 1
    new_tuples_str = []
    
    date_str = "2026-07-27 12:00:00"
    for i, req in enumerate(new_reqs):
        max_id += 1
        req_json = json.dumps({"en": req, "ceb": req, "fil": req}).replace('"', '\\"')
        # id, service_id, requirement_text, is_required, display_order, created_at, updated_at
        new_tuple = f"{max_id},1,'{req_json}',1,{i+1},'{date_str}','{date_str}'"
        new_tuples_str.append(new_tuple)
        
    all_tuples = new_tuples_str + kept_tuples
    # sort by id? not strictly necessary, but let's just prepend them and maybe fix ids?
    # actually it's fine.
    
    new_values_str = "),\n(".join(all_tuples)
    new_values_str = "(" + new_values_str + ");"
    
    new_sql = sql[:match.start()] + 'INSERT INTO `service_requirements` VALUES\n' + new_values_str + sql[match.end():]
    
    with open('database_export.sql', 'w', encoding='utf-8') as f:
        f.write(new_sql)
    print("Updated database_export.sql")
else:
    print("Could not find service_requirements inserts.")

