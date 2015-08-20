<?php

use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;
use Behat\Mink\Exception\DriverException;

use Behat\MinkExtension\Context\MinkContext;
use Behat\Testwork\Hook\Scope\AfterSuiteScope;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;

/**
 * Features context.
 */
class FeatureContext extends MinkContext implements SnippetAcceptingContext{

    const FNAME = 'configuration.php';

    const FNAME_BACKUP = 'configuration.php.bk';

    const FNAME_TEST = 'configuration.php.test';

    const SQLFILW = 'integraDBClear200815.sql';

    const dbname = 'integra_testdb';
    const dbuser = 'testUser';
    const dbpass = 'pa55_4testUser';

    const LIQUIBASE_JAR = 'C:\wamp\www\liquibase\mysql-connector-java-5.1.34-bin.jar';

    const DIRS = array('tmp/tests', 'logs/tests');

    /**
     * @BeforeSuite
     */
    public static function prepare(BeforeSuiteScope $scope)
    {
        // prepare system for test suite
        // before it runs
        if (file_exists(self::FNAME) ) {
            rename(self::FNAME, self::FNAME_BACKUP);
            copy(self::FNAME_TEST, self::FNAME);
        }
        foreach (self::DIRS as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir);
            }
        }

        system('mysql -u '.self::dbuser.' -p'.self::dbpass.' '.self::dbname.' < sql_tim/'.self::SQLFILW);
        system('liquibase --driver=com.mysql.jdbc.Driver --classpath='.self::LIQUIBASE_JAR.' --changeLogFile=sql_tim\changelog_integradora.sql --url="jdbc:mysql://localhost/'.self::dbname.'" --username='.self::dbuser.' --password="'.self::dbpass.'" migrate');
    }

    /**
     * @AfterSuite
     */
    public static function clean(AfterSuiteScope $scope)
    {
        // clean system for test suite
        // after it runs
        if (file_exists(self::FNAME_BACKUP) ) {
            rename(self::FNAME_BACKUP, self::FNAME);
        }

        foreach (self::DIRS as $dir) {
            if (is_dir($dir)) {
                foreach(scandir($dir) as $file) {
                    unlink($file);
                }
                rmdir($dir);
            }
        }
    }

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

    /**
     * @Given /^I wait for "([^"]*)" seconds$/
     */
    public function iWaitForSeconds($arg1)
    {
        $this->getSession()->wait($arg1 * 1000);
    }
}
