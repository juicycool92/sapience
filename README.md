# sapience project
사피엔스 프로젝트 -Jeong Changwon
SlimFramework를 이용한 게시판 짜기

usage : 
> composer install


DB : MariaDB 10.4
```
create table board
(
	no int auto_increment
		primary key,
	user_id varchar(60) not null,
	subject varchar(180) not null,
	content varchar(4096) default '' null,
	create_datetime datetime default current_timestamp() null
);
``` 
```
create table test
(
	user_id varchar(60) not null
		primary key,
	user_name varchar(80) not null,
	password varchar(512) not null,
	reg_datetime datetime default current_timestamp() null
);
```


