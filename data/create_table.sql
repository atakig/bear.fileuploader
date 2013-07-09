      create table upload_files(
        id INTEGER PRIMARY KEY,
        tmp_filename TEXT,
        upload_filename TEXT,
        mime TEXT,
        ext TEXT,
        memo TEXT,
        ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      );

     create table upload_files_bkup(
       id INTEGER,
       tmp_filename TEXT,
       upload_filename TEXT,
       mime TEXT,
       ext TEXT,
       memo TEXT,
       ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP
     );
