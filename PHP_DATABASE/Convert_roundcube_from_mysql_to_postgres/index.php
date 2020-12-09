<?php
// Для конвертування дані із таблиць roudcube mysql бази даних експортуються через phpmyadmin у php масиви та обгортаються відповідним класом, також можна напряму підключитись скриптом до бази, та витягнути таблиці
require_once 'Querys.php';
require_once ('roundcube_new_table_session.php');
require_once ('roundcube_new_table_identities.php');
require_once ('roundcube_new_table_contacts.php');
require_once ('roundcube_new_table_contactgroups.php');
require_once ('roundcube_new_table_contactgroupmembers.php');
require_once ('roundcube_new_table_users.php');
// Коннект до БД 
$conn_string = "host=localhost port=5432 dbname=testbase user=some_admin password=123456789";
$dbconnPg = pg_connect($conn_string);

   if (!$dbconnPg) {
   echo "Error occured\n";
   exit;
    }else{
// Створєються об'єкт для виклику методу обробки масивів та запису в базу
$query = new Querys();
// Створюються об'єкти масивів бази даних формату PHP
$ses_array = new SessionTb();
$ident_array = new Identities();
$contact_array = new Contacts();
$contGrups_array = new Contactgroups();
$contGrupsMember_array = new Contactgroupmembers();
$users_array = new Users();

// Спочатку потрібно заповнити таблицю юзерів із головним ключем, потім заповнюються інші таблиці язі залежать від головного ключа
$query->sendQuery($users_array->users, $dbconnPg,'users');
$query->sendQuery($ses_array->session, $dbconnPg,'session');
$query->sendQuery($ident_array->identities, $dbconnPg,'identities');
$query->sendQuery($contact_array->contacts, $dbconnPg,'contacts');
$query->sendQuery($contGrups_array->contactgroups, $dbconnPg,'contactgroups');
$query->sendQuery($contGrupsMember_array->contactgroupmembers, $dbconnPg,'contactgroupmembers');

// Чистка бази від старих mail_host які вже не дійсні
pg_query($dbconnPg, "DELETE FROM users where mail_host = 'old.smtp'");

 pg_close($dbconnPg);
}