SELECT t.tablespace_name, t.status, f.file_name, f.status 
FROM dba_data_files f, dba_tablespaces t 
WHERE f.tablespace_name = t.tablespace_name 
UNION 
SELECT t.tablespace_name, t.status, f.file_name, f.status 
FROM dba_tablespaces t, dba_temp_files f 
WHERE f.tablespace_name = t.tablespace_name;

SELECT f.tablespace_name, SUM(f.bytes) / 1024000 AS Taille_totale, SUM(free.bytes) / 1024000 AS libre, 
(SUM(f.bytes) - SUM(free.bytes)) / 1024000 AS Utilise, ROUND(100 * (SUM(f.bytes) - SUM(free.bytes)) / SUM(f.bytes), 2) AS pourcentage_utilisation
FROM dba_data_files f, dba_free_space free
WHERE f.tablespace_name = free.tablespace_name
GROUP BY f.tablespace_name
UNION
SELECT f.tablespace_name, SUM(f.bytes) / 1024000 AS Taille_totale, SUM(free.bytes_free) / 1024000 AS libre, 
SUM(free.bytes_used) / 1024000 AS Utilise, ROUND(100 * SUM(free.bytes_used) / SUM(f.bytes), 2) AS pourcentage_utilisation
FROM dba_temp_files f, v$temp_space_header free
WHERE f.tablespace_name = free.tablespace_name
GROUP BY f.tablespace_name;

SELECT u.username, u.created, u.account_status, u.default_tablespace, u.temporary_tablespace
FROM dba_users u, v$database d
WHERE TRUNC(u.created) = TRUNC(d.created);

SELECT o.owner, o.object_type, count(o.object_type) AS tot
FROM dba_objects o, dba_users u
WHERE o.owner = u.username
GROUP BY o.owner, o.object_type
ORDER BY o.owner, o.object_type;  

create or replace
PACKAGE RefCursors_Pkg AS
  TYPE r_cursor IS REF CURSOR;
  PROCEDURE TablespaceInfos(ref_cursor OUT r_cursor);
END RefCursors_Pkg;

create or replace
PACKAGE BODY RefCursors_Pkg AS

  PROCEDURE TablespaceInfos
  (ref_cursor OUT r_cursor)
  IS
  BEGIN
      OPEN ref_cursor FOR
        SELECT *
        FROM user_users;
  END TablespaceInfos;
  
END RefCursors_Pkg;