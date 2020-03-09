DROP TABLE IF EXISTS organization CASCADE;

CREATE TABLE organization (
    org_name varchar(255) PRIMARY KEY,
    parent_org_name varchar(255)
);

