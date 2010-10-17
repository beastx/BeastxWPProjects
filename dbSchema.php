<?
$BeastxWPProjectsDBSchema = array(   //  si se declara un dbSchema es usado para generar las tablas automaticamente al momento de hacer activate y de destruirla en caso del deactivate
    array(
        'tableName' => 'categories',
        'schema' => "
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            categorySlug VARCHAR(55) NOT NULL,
            categoryName VARCHAR(55) NOT NULL,
            enabled mediumint(9),
            PRIMARY KEY (id),
            UNIQUE KEY (categorySlug)"
    ),
    array(
        'tableName' => 'licences',
        'schema' => "
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            licenceName VARCHAR(55) NOT NULL,
            licenceUrl VARCHAR(55) NOT NULL,
            enabled mediumint(9),
            UNIQUE KEY id (id)"
    ),
    array(
        'tableName' => 'uploads',
        'schema' => "
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            itemId mediumint(9),
            path VARCHAR(55) NOT NULL,
            version VARCHAR(55) NOT NULL,
            UNIQUE KEY id (id)"
    ),
    array(
        'tableName' => 'relatedPosts',
        'schema' => "
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            postId mediumint(9),
            itemId mediumint(9),
            UNIQUE KEY id (id)"
    ),
    array(
        'tableName' => 'stats',
        'schema' => "
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            itemId mediumint(9),
            ip VARCHAR(55),
            userAgent VARCHAR(55),
            referer VARCHAR(55),
            date datetime,
            isDownloadStat int(2) DEFAULT 0,
            UNIQUE KEY id (id)"
    )
);
?>