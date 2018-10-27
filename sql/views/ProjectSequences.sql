drop view ProjectSequences;
create view ProjectSequences AS
select PSM.project_id, P.site_id, P.photo_id, P.taken, P.uploaded from Photo P
inner join ProjectSiteMap PSM on P.site_id = PSM.site_id
where P.photo_id >= PSM.start_photo_id
and PSM.end_photo_id is NULL
and P.sequence_num = 1
union
select PSM.project_id, P.site_id, P.photo_id, P.taken, P.uploaded from Photo P
inner join ProjectSiteMap PSM on P.site_id = PSM.site_id
where P.photo_id >= PSM.start_photo_id
and P.photo_id <= PSM.end_photo_id
and P.sequence_num = 1