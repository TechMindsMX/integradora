Feature: Login from index page
  I am login from page index and go to select Integrado

  @javascript
  Scenario: I am login from Index
    Given I am on "index.php"
    And I press "Ingresar"
    And I fill in "Usuario" with "luis"
    And I fill in "Contraseña" with "Luis#01prueba"
    And I press "Submit"
    Then I wating for a "Modal Window"
    When I am on "index.php"
    Then I follow "Administración"
    And I follow "Operar con otro integrado"
