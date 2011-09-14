<?php

    //
    class Setup_Controller extends Template_Controller {
        
        // Set the name of the template to use
        public $template = "wi3/workplace";

        public function index() {
            $this->template->title = "Wi3 Tabellen Setup";
            $this->scan_models();
            $ret = "<h2>Tabellen Setup</h2>";
            $ret .= "<p>welkom bij de Setup! Hier kan je je Databasetabellen aanmaken en bestaande tabellen wijzigen of verwijderen.</p>";
            
            $ret .= "<table>";
            foreach($this->model_names as $model) {
                $ret .= "<tr><td>" . $model . "</td><td>" . html::anchor("setup/setup/" . inflector::plural($model) , "setup") . "</td><td>" . html::anchor("setup/update/" . inflector::plural($model) , "update") . "</td></tr>";
            }
            $ret .= "</table>";
            $ret .= "<p> </p>";
            $ret .= "<p><a href='?deletefile=true'>Dit bestand verwijderen</a>, zodat het niet meer toegankelijk is voor kwaadwillende derden. <strong>Doe dit altijd na setup!</strong></p>";
            $this->template->content = $ret;
            
            if (isset($_GET["deletefile"]) AND $_GET["deletefile"] == "true") {
                if (unlink(Wi3::$pathof->wi3 . "controllers/setup.php")) {
                    $this->template->content = "<strong>Setupbestand succesvol verwijderd!</strong>";   
                } else {
                    $this->template->content = "<strong>Setupbestand kong *niet* verwijderd worden!</strong>";
                }
            }
            
        }
        
        function update($model) {
            $DButils = new DButils();
            $DButils->db = Database::instance();
            $this->template->content = $DButils->update_table($model); //just add columns if they don't exist...
            $this->template->content .= "<p>" . html::anchor("setup", "terug") . "</p>";
        }
        
        function gotoversion($version) {
            if ($version == 1) {
                //update page Model and fill in all these url-s
                $this->update("pages");
                $DButils = new DButils();
                $DButils->db = Database::instance();
                $DButils->db->query("UPDATE pages SET url = title");
            }
        }
        
        function setup($model) {
            
            $DButils = new DButils();
            $DButils->db = Database::instance();
            //check if the to be used database exists and create, if it doesn't
            $dbname = Kohana::config('database.default.connection.database');
            try {
                $DButils->db->query("SELECT IF(EXISTS (SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . $dbname . "'), 'Yes','No') AS 'check'");
            } 
            catch(Exception $e) {
                //create database if check fails
                //creating databases should be done in 'raw' mysql_query
                $user=Kohana::config('database.default.connection.user');
                $password=Kohana::config('database.default.connection.pass');
                $host=Kohana::config('database.default.connection.host');
                echo "Database '" . $dbname . "' bestond nog niet ";
                mysql_connect($host,$user,$password);
                mysql_query("CREATE DATABASE IF NOT EXISTS " . $dbname) or die ("en kon ook niet aangemaakt worden."); 
                echo "en is aangemaakt!";
                mysql_close();
            }
            /*$check = $DButils->db->query("SELECT IF(EXISTS (SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . $dbname . "'), 'Yes','No') AS 'check'");
            if ($check[0]->check == "No") { 
                echo "Database '" . $dbname . "' bestond nog niet ";
                $DButils->db->query("CREATE DATABASE IF NOT EXISTS " . $dbname) or die ("en kon ook niet aangemaakt worden."); 
                echo "en is aangemaakt!";
            } else {
                echo $check[0]->check;
            }*/
            
            if ($model == "roles") {
                $DButils->setup_table('roles', true);
                //create common roles
                $role = new Role_Model(); $role->id = 1; $role->name = 'login'; $role->save();
                $role = new Role_Model(); $role->id = 2; $role->name = 'admin'; $role->save();
                $role = new Role_Model(); $role->id = 3; $role->name = 'siteadmin'; $role->save();
            } else
            
            if ($model == "users") {
                $DButils->db->query("DROP TABLE IF EXISTS users");
                $DButils->db->query("CREATE TABLE IF NOT EXISTS users (
  id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  email varchar(127) NOT NULL,
  username varchar(32) NOT NULL DEFAULT '',
  password char(50) NOT NULL,
  logins int(10) UNSIGNED NOT NULL DEFAULT 0,
  last_login int(10) UNSIGNED,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_username (username),
  UNIQUE KEY uniq_email (email)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;");
                $DButils->setup_pivot_tables('users'); //for generating the pivot tables
                $DButils->db->query("ALTER TABLE roles_users ADD CONSTRAINT roles_users_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE, ADD CONSTRAINT roles_users_ibfk_2 FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE CASCADE;");
                
                //create ADMIN user
                $user = ORM::factory("user");
                $user->username = "admin";
                $user->email = "admin@domain.com";
                $user->password = "admin";
                
                 //ORM::factory('role', 'login') returns orm object and get's value from colum with name=login
                //$user->add creates a relation between $user and role orm model returned by ORM::factory('role', 'login')
                if ($user->add(ORM::factory('role', 'login')) AND $user->add(ORM::factory('role', 'admin')) AND $user->save()) {
                    $this->template->content = "<p><strong>Admin</strong> user created.</p>";
                }
                
            } else

            if ($model == "user_tokens") {
                $DButils->setup_table('user_tokens', true); //also setup 
                $DButils->db->query("ALTER TABLE user_tokens  ADD CONSTRAINT user_tokens_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE;");
            } else
            
            {
                $DButils->setup_table($model, true);
            }
              
            $this->template->content = "<p><strong>" . $model . "</strong> table (re)created.</p><p>" . html::anchor("setup", "terug") . "</p>";
            
        }
        
        /**
         * Scan models from models folder
         *
         * @return void
         */
        private function scan_models()
        {
            $files = Kohana::list_files('models');

            $this->model_names = Array();
            foreach($files as $file)
            {
                preg_match('/.*\/(?!formo|auth|base)([^\/]*)'.EXT.'$/', $file, $match);
                if (!empty($match)) {
                    $this->model_names[] = $match[1];
                }
            }
            return $this->model_names;
        }
        
        //this functions makes sure that any views are loaded in the Wi3::$workplace namespace
        //so the $this in these views refers to Wi3::$workplace
        public function _kohana_load_view($kohana_view_filename, $kohana_input_data)
        {
            //we want the pagetemplate (and other templates) to be available through the Wi3_workplace namespace
            return Wi3::$workplace->_kohana_load_view($kohana_view_filename, $kohana_input_data);
        }
        
    }
    
    
?>