<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\DriverException;

use Behat\MinkExtension\Context\MinkContext;

/**
 * Features context.
 */
class FeatureContext extends MinkContext implements SnippetAcceptingContext{
    /**
     * @Then I should go to :arg1
     */
    public function iShouldGoTo($arg1){
        $currUrl = explode('/', $this->getSession()->getCurrentUrl());

        if(strpos($currUrl[4],$arg1) === false) {
            throw new \Symfony\Component\Config\Definition\Exception\Exception;
        }
    }

    /**
     * @Given I am logged whith user :usuario and pass :password
     */
    public function iAmLoggedWhithUserAndPass($usuario, $password){
        $this->visit('index.php?option=com_users&view=login');
        $this->fillField('Usuario',$usuario);
        $this->fillField('Contraseña',$password);
        $this->pressButton('Ingresar');
        $this->iShouldGoTo('index.php?option=com_users&view=profile');
        $this->clickLink('Administración');
    }

    /**
     * @When Yo deberia de ir a esta pinche pagina :url
     */
    public function yoDeberiaDeIrAEstaPinchePagina($url){
        $this->iShouldGoTo($url);
    }

    /**
     * @When I select :option from :select
     */
    public function iSelectFrom($option, $select){
        $this->selectOption($select, $option);
    }

    /**
     * @Then I should see :texto
     */
    public function iShouldSee($texto){
        $this->assertPageContainsText($texto);
    }

    /**
     * @Then I wating for a :arg1
     */
    public function iWatingForA($arg1){
        $this->getSession()->wait(5000);
    }

    /**
     * @Given I am logged in admin with user :arg1 and  pass :arg2
     */
    public function iAmLoggedInAdminWithUserAndPass($arg1, $arg2){
        $this->visit('/administrator');
        $this->fillField('username',$arg1);
        $this->fillField('passwd',$arg2);
        $this->pressButton('Conectar');;
    }

    /**
     * @Then I check radio with value :arg1
     */
    public function iCheckRadioWithValue($arg1){
        $jquery = 'jQuery.each(jQuery("input:radio"),function(key, value){
            var inputRadio = jQuery(this);
            if(inputRadio.val() == "'.$arg1.'"){
                jQuery(this).prop("checked", true);
            }
        })';
        $this->getSession()->executeScript($jquery);
    }
}
