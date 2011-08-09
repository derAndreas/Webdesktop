[bootstrap]
autoloadernamespaces[] = "Zend_"
autoloadernamespaces[] = "App_"

[production]
;show errors
phpSettings.display_startup_errors = 0
phpSettings.display_errors = 0
includePaths.library = APPLICATION_PATH "/../library"
bootstrap.path = APPLICATION_PATH "/Bootstrap.php"
bootstrap.class = "Bootstrap"
;frontcontroller
resources.frontcontroller.moduledirectory = APPLICATION_PATH "/modules"
resources.frontcontroller.defaultmodule = "noc"
resources.frontcontroller.throwexceptions = false 
resources.frontcontroller.params.prefixDefaultModule = true
; multi module
resources.layout.layoutPath = APPLICATION_PATH "/layouts"
resources.modules[] =
resources.session.name = "webdesktop"
;default db adapter
resources.db.password_salt = 
resources.db.adapter = "PDO_MYSQL"
resources.db.isdefaulttableadapter = true
resources.db.params.dbname = <CHANGEHERE> 
resources.db.params.username = <CHANGEHERE> 
resources.db.params.password = <CHANGEHERE> 
resources.db.params.hostname = "localhost"
resources.db.params.driver_options.1002 = "SET NAMES UTF8;"

; need a default user so that ACL does not run into infinite loop
; id is from DB
acl.role.default.id = 1
acl.role.default.name = "Guest"

;acl.controller.hooks.path = APPLICATION_PATH "/../library/App/Model/Acl/Controller/Hook/"
acl.controller.hooks.webdesktop.name = App_Model_Acl_Controller_Hook_Webdesktop
acl.controller.hooks.webdesktop.path = APPLICATION_PATH "/modules/webdesktop/models/Modules/"
acl.controller.hooks.webdesktop.skip[] = Abstract.php

[staging : production]

[testing : production]
phpSettings.display_startup_errors = 1
phpSettings.display_errors = 1

[development : production]
phpSettings.display_startup_errors = 1
phpSettings.display_errors = 1
resources.frontController.params.displayExceptions = 1
