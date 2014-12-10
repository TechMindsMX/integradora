--liquibase formatted sql

--changeset ricardolyon:1
create table person (
  id int not null primary key,
  firstname varchar(80),
  lastname varchar(80) not null,
  state varchar(2)
);

--rollback drop table person

--changeset ricardolyon:2
drop table person;

