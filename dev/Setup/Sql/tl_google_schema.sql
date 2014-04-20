create table tl_google
(
  id                int not null auto_increment,
  identity          varchar(255), -- google account username
  refresh_token     varchar(255),
  primary key (id)
);
