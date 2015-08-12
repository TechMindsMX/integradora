Feature: Login
  Go to login page and login successful

  @javascript
  Scenario: Go to the login page and login
    Given I am on "index.php?option=com_users&view=login"
    When I fill in "Usuario" with "luis"
      And I fill in "Contraseña" with "Luis#01prueba"
      And I press "Ingresar"
    Then Yo deberia de ir a esta pinche pagina "index.php?option=com_users&view=profile"

  @javascript
  Scenario Outline: Once I am logged in, I should select Integrado
    Given I am logged whith user <user> and pass <pass>
      And I follow "Operar con otro integrado"
    When Yo deberia de ir a esta pinche pagina "index.php?option=com_integrado&view=integrado&layout=change"
      And I select <integrado> from <campos>
      And I press "Aceptar"
    Then I should see text matching <texto>

    Examples:
    | user   | pass            | integrado | campos        | texto                           |
    | "luis" | "Luis#01prueba" | "Prueba   | "integradoId" | "Esta operando con "Prueba""    |