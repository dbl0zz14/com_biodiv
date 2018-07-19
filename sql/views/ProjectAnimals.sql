# view including first and last photo id and timestamps of both.
create view ProjectAnimals as
select A.animal_id, PSM.project_id, A.person_id, P.site_id, S.site_name, P.upload_filename as start_filename, P.photo_id as start_photo_id, P.taken as start_taken, P2.photo_id as end_photo_id, P2.taken as end_taken, S.grid_ref, O.option_name as species, O2.option_name as age, O3.option_name as gender, A.number, A.timestamp as classify_time 
from Animal A 
inner join Photo P on A.photo_id = P.photo_id and P.sequence_num = 1
inner join PhotoSequence PS on P.photo_id = PS.start_photo_id
inner join Photo P2 on PS.end_photo_id = P2.photo_id
inner join Site S on P.site_id = S.site_id
inner join Options O on O.option_id = A.species
left join Options O2 on O2.option_id = A.age
left join Options O3 on O3.option_id = A.gender
inner join ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and P.photo_id <= PSM.end_photo_id 
UNION 
select A.animal_id, PSM.project_id, A.person_id, P.site_id, S.site_name, P.upload_filename as start_filename, P.photo_id as start_photo_id, P.taken as start_taken, P2.photo_id as end_photo_id, P2.taken as end_taken, S.grid_ref, O.option_name as species, O2.option_name as age, O3.option_name as gender, A.number, A.timestamp as classify_time 
from Animal A 
inner join Photo P on A.photo_id = P.photo_id and P.sequence_num = 1
inner join PhotoSequence PS on P.photo_id = PS.start_photo_id
inner join Photo P2 on PS.end_photo_id = P2.photo_id
inner join Site S on P.site_id = S.site_id
inner join Options O on O.option_id = A.species
left join Options O2 on O2.option_id = A.age
left join Options O3 on O3.option_id = A.gender
inner join ProjectSiteMap PSM on P.site_id = PSM.site_id and P.photo_id >= PSM.start_photo_id and PSM.end_photo_id is null 
ORDER BY `animal_id` ASC 