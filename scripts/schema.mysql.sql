create table if table not exists  Outage (
    id bigint(20) not null auto_increment,
    Company_RecID bigint(20) not null,
    
--outage type
type_id

--start time
start_time

--estimated end time (optional)
end_time

--time zone
time_zone_id

--affected service
service_id

--availability
availability_id

--summary
summary


);
