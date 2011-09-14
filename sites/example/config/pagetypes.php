<?php

    $config = 
    Array(
        "version" => "1.0",
        "pagetypes" => Array(
            "standard" => Array(
            
                "pagetype" => "standard",
                "title" => "tekst pagina",
                "description" => "een standaard tekstpagina",
                "pagefiller" => "default",
                //the next part is specific for the default pagefiller, the 'field' pagefiller
                //and is used to determine what fields should be created for a certain page
                "pagefillerspecific" => Array(
                    "dropzones" => Array(
                        //this specifies the allowed and default content of the mainContent dropzone
                        "mainContent" => Array(
                            "allowedFields" => Array("component_text"),
                            "defaultFields" => Array("component_text") //these fields will be created by default when a new page is created
                        )
                    )
                )
            ), "showbox" => Array(
            
                "pagetype" => "showbox",
                "title" => "etalage",
                "description" => "een aantrekkelijke portfolio-etalage",
                "pagefiller" => "default",
                //the next part is specific for the default pagefiller, the 'field' pagefiller
                //and is used to determine what fields should be created for a certain page
                "pagefillerspecific" => Array(
                    "dropzones" => Array(
                        //this specifies the allowed and default content of the mainContent dropzone
                        "mainContent" => Array(
                            "allowedFields" => Array("component_text", "component_showbox"),
                            "defaultFields" => Array("component_text", "component_showbox") //these fields will be created by default when a new page is created
                        )
                    )
                ),
                
            )
        )
    );
    
?>
