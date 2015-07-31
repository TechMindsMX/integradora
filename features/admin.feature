Feature: Administrator test
  I am go to administrator page and make a success login
  I am change integrado status

  @javascript
  Scenario: Login into administrator page
    Given I am on "/administrator/index.php"
    When I fill in "username" with "Thor"
    And I fill in "passwd" with "ADMINt1m2014"
    And I press "Conectar"
    And I wating for a "algo"
    Then Yo deberia de ir a esta pinche pagina "administrator"

  @javascript
  Scenario: Change integrado status
    Given I am logged in admin with user "thor" and  pass "ADMINt1m2014"
    Then I follow "Administración de Integrados"
    When I check radio with value "95647168a20e4878b8813499cdc7c220"
      And I press "Parametrización"
      And I fill in "params" with "1"
      And I check "factura_pagada"
      And I check "odc_pagada"
      And I check "odr_pagada"
      And I check "odd_pagada"
      And I press "Guardar y cerrar"
      And I wating for a "Send data and change page"
    Then I should see text matching "Datos Almacenados"
      And I check radio with value "95647168a20e4878b8813499cdc7c220"
    When I press "Validación de Integrado"
      And I check "integrado_datos_personales_nombre_representante"
      And I check "integrado_datos_personales_nacionalidad"
      And I check "integrado_datos_personales_sexo"
      And I check "integrado_datos_personales_fecha_nacimiento"
      And I check "integrado_datos_personales_rfc"
      And I check "integrado_datos_personales_calle"
      And I check "integrado_datos_personales_num_exterior"
      And I check "integrado_datos_personales_num_interior"
      And I check "integrado_datos_personales_cod_postal"
      And I check "integrado_datos_personales_curp"
      And I check "integrado_datos_personales_email"
      And I check "integrado_datos_personales_tel_fijo"
      And I check "integrado_datos_personales_tel_fijo_extension"
      And I check "integrado_datos_personales_tel_movil"
      And I check "integrado_datos_personales_nom_comercial"
      And I follow "Datos empresa (solo persona moral)"
      And I check "integrado_datos_empresa_razon_social"
      And I check "integrado_datos_empresa_rfc"
      And I check "integrado_datos_empresa_calle"
      And I check "integrado_datos_empresa_num_exterior"
      And I check "integrado_datos_empresa_num_interior"
      And I check "integrado_datos_empresa_cod_postal"
      And I check "integrado_datos_empresa_tel_fijo"
      And I check "integrado_datos_empresa_tel_fijo_extension"
      And I check "integrado_datos_empresa_tel_fax"
      And I check "integrado_datos_empresa_sitio_web"
      And I follow "Datos bancarios"
      And I check "integrado_datos_bancarios_banco_cuenta"
      And I check "integrado_datos_bancarios_banco_sucursal"
      And I check "integrado_datos_bancarios_banco_clabe"
      And I check "integrado_datos_bancarios_bankName"
      And I follow "Autorizaciones"
      And I check "integrado_params_params"
      And I press "Guardar y cerrar"
    Then I should see text matching "El elemento ha sido enviado correctamente."
      And I wating for a "que vean que si se guardo y mando el mensaje"