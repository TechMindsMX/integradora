Feature: Login from index page
  I am login from page index and go to select Integrado

  @javascript
  Scenario: I am login from Index
    Given I am on "index.php"
      And I fill in "Usuario" with "luis"
      And I fill in "Contraseña" with "Luis#01prueba"
      And I press "Ingresar"
    When I am on "index.php"
    Then I follow "Administración"
      And I Click over integradoraButton "Operar con otro integrado"
      And I select option "Integrado1" select "integradoId"
      And I wait for "5" seconds
    And I press "Aceptar"
    Then I should see text matching "Integrado1"
