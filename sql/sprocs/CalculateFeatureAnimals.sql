DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `CalculateFeatureAnimals`()
    NO SQL
    DETERMINISTIC
BEGIN

    /* First get a list of all the sites - miss out private ones by checking SiteAnimals table */

    create temporary table FeatureSites
    select S.site_id, F.feature_id
    from Site S
    left join Features F
    on S.latitude between F.west and F.east and S.longitude between F.south and F.north;

	select * from FeatureSites;





END$$
DELIMITER ;
