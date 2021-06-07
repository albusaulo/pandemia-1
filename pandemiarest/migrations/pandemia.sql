create database pandemia;

create table if not exists person (
  id BIGINT not null primary key auto_increment unique,
  name varchar(255) not null,
  cpf varchar(14) not null
)ENGINE = innodb default CHARSET=utf8;

create table if not exists job (
  id BIGINT not null primary key auto_increment unique,
  type_job varchar(10) not null,
  id_person BIGINT not null,
  status varchar(20) not null,
  created_at datetime not null,
  updated_at date,
  dayD date
)ENGINE = innodb default CHARSET=utf8;

alter table job add foreign key (id_person) references person(id);