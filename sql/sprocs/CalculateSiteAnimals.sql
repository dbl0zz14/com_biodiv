DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `CalculateSiteAnimals`()
BEGIN

	/* First get all the classifications with some photo details */
    create temporary table SiteSequences
    SELECT P.site_id as site_id, 
    	A.species as option_id, 
        1 as number, 
        P.photo_id as photo_id, 
        P.sequence_id as sequence_id,
        year(P.taken) as year_taken
    from Animal A 
    inner join Photo P on P.photo_id = A.photo_id and P.sequence_num = 1
    where A.species!= 97;
    
    /* Expand to include the projects each classification is in */
    create temporary table ProjectSequences
    SELECT PSM.project_id as project_id, 
    	SS.site_id as site_id, 
        SS.sequence_id as sequence_id,
        SS.option_id as option_id,
    	SS.year_taken as year_taken, 
        SS.number as num_animals
    from SiteSequences SS 
    inner join ProjectSiteMap PSM on PSM.site_id = SS.site_id and SS.photo_id >= PSM.start_photo_id and SS.photo_id <= PSM.end_photo_id;
   
    insert into ProjectSequences
    SELECT PSM.project_id as project_id, 
    	SS.site_id as site_id, 
        SS.sequence_id as sequence_id,
        SS.option_id as option_id,
    	SS.year_taken as year_taken, 
        SS.number as num_animals 
    from SiteSequences SS 
    inner join ProjectSiteMap PSM on PSM.site_id = SS.site_id and SS.photo_id >= PSM.start_photo_id and PSM.end_photo_id is NULL;
    
    /* Remove any private project rows after inserting into private projects table */
	create temporary table PrivateProjectSequences
	SELECT *
	from ProjectSequences 
	WHERE project_id in (select project_id from Project where access_level > 2);
	
    DELETE from ProjectSequences
    WHERE project_id in (select project_id from Project where access_level > 2);
    
    /* Get the max number spotted for each sequence */
    create TEMPORARY table Sightings
    select PS.site_id as site_id,
    	PS.sequence_id as sequence_id, 
    	PS.option_id as option_id,
    	PS.year_taken as year_taken, 
        max(PS.num_animals) as num_animals
    from ProjectSequences PS
    group by PS.site_id, PS.sequence_id, PS.option_id, PS.year_taken;
    
    /* Repeat for private projects */
    create TEMPORARY table PrivateSightings
    select PS.site_id as site_id,
    	PS.sequence_id as sequence_id, 
    	PS.option_id as option_id,
    	PS.year_taken as year_taken, 
        max(PS.num_animals) as num_animals
    from PrivateProjectSequences PS
    group by PS.site_id, PS.sequence_id, PS.option_id, PS.year_taken;
    
    
    /* Update the SiteAnimals statistics table */
    TRUNCATE table SiteAnimals;
    
    insert into SiteAnimals (site_id, species, year_taken, num_sightings)
    select S.site_id,
    	S.option_id,
        S.year_taken,
        sum(S.num_animals)
    from Sightings S
    group by S.site_id, S.option_id, S.year_taken;
    
    /* Update the SiteAnimals statistics table */
    TRUNCATE table PrivateSiteAnimals;
    
    insert into PrivateSiteAnimals (site_id, species, year_taken, num_sightings)
    select S.site_id,
    	S.option_id,
        S.year_taken,
        sum(S.num_animals)
    from PrivateSightings S
    group by S.site_id, S.option_id, S.year_taken;
    
	drop temporary table SiteSequences;
    drop temporary table ProjectSequences;
    drop temporary table Sightings;
	drop temporary table PrivateProjectSequences;
    drop temporary table PrivateSightings;
    
END$$
DELIMITER ;
