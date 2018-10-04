<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-rma
 * @version   2.0.25
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Rma\Test\Unit\Observer;

use Esperance\Assertion;
use Esperance\Extension;
use PHPUnit_Framework_TestCase;

/**
 * @codingStandardsIgnoreFile
 * @SuppressWarnings(PHPMD)
 */
class PHPUsableTest extends \PHPUnit_Framework_TestCase
{
    protected $_args = array();
    protected $_result = null;
    protected $_assertion_count = null;
    protected $_before_chain = array();
    protected $_setup_callback = null;
    protected $_teardown_callback = null;
    protected $_describe_title_chain = array();
    protected $_describe_chain_is_disabled = false;
    protected $_describe_chain_disabled_index = -1;
    protected $_current_test_name = "Unknown test";

    private $_shadow_mocks = array();

    protected static $test_suite = array();
    public static $current_test = null;

    /**
     * This is just a helper method to use when a method is being called against
     * the current test.  In particular this was written to make the global
     * aliases (e.g. 'describe') more DRY.
     **/
    static public function run_on_current_test($method_name, $arguments) 
    {
        return call_user_func_array(array(PHPUsableTest::$current_test, $method_name), $arguments);
    }

    /**
     * This is part of the API for PHPUnit_Framework_Test, and is necessary for when phpunit runs
     * the class.  This generates a series of 'Test' instances, one for each of
     * the 'it' statements, and returns the TestSuite.
     **/
    static public function suite() 
    {
        $suite = new \PHPUnit_Framework_TestSuite;
        $klass = get_called_class();
        $base_instance = new $klass;
        $base_instance->setUp();
        $base_instance->tests();

        $test_array = self::$test_suite;
        self::$test_suite = array();

        foreach($test_array as $test) {
            $suite->addTest($test);
        }
        return $suite;
    }


    /**
     * Magic aliasing method so we can call assertions against the test class instead of
     * against the static PHPUnit_Framework_Assert methods.
     **/
    public function __call($method_name, $args) 
    {
        /*
        if(method_exists('PHPUnit_Framework_Assert', $method_name)) {
            $this->addToAssertionCount(1);
            return call_user_func_array(array('PHPUnit_Framework_Assert', $method_name), $args);
        } else {
            throw new Exception( " Method " . $method_name . " not exist in this class " . get_class( $this ) . "." );
        }
         */
        throw new \Exception( " Method " . $method_name . " not exist in this class " . get_class( $this ) . "." );
    }

    /** This is a shim so that we can use the PHPUnit Test Case RunTest
     * method to execute our own tests.  See the PHPUnit TestCase source
     * lines 963
     *  try {
     *      $class  = new ReflectionClass($this);
     *      $method = $class->getMethod($this->name);
     *  }
     *
     *
     */
    public function run_current_test() 
    {
        return call_user_func($this->_current_test, $this);
    }
    /**
     * Plumbing to connect up esperance to give us an expectation syntax for
     * the assertions in our tests.
     *
     * @example $test->expect(method_exists($test->key_value_store, 'set'))->to->be->ok();
     **/
    public function expect($subject)
    {
        $extension = new Extension;
        $self = $this;
        $extension->beforeAssertion(function () use ($self) {
            $self->addToAssertionCount(1);
        });
        return new Assertion($subject, $extension);
    }

    /**
     * The nested context dsl command to create a new level of context.
     **/
    public function describe($title, $body = null) 
    {
        if($this->_result === null) {
            $this->_result = new \PHPUnit_Framework_TestResult;
        }

        array_push($this->_before_chain, null);
        array_push($this->_describe_title_chain, $title);

        if ($body == null) {
            $this->it('not implemented');
        } else {
            call_user_func($body, $this);
        }

        array_pop($this->_before_chain);
        array_pop($this->_describe_title_chain);

        if ($this->_describe_chain_is_disabled && $this->_describe_chain_disabled_index == count($this->_describe_title_chain)) {
            $this->_describe_chain_is_disabled = false;
        }
    }

    public function xdescribe($title, $body = null) 
    {
        $this->_describe_chain_is_disabled = true;
        $this->_describe_chain_disabled_index = count($this->_describe_title_chain);
        $this->describe($title, $body);
    }

    /**
     * The nested context dsl command to create a setup callback that
     * will run before each test in the current or lower contexts.
     **/
    public function before($callback) 
    {
        array_pop($this->_before_chain);
        array_push($this->_before_chain, $callback);
    }

    /**
     * This will stick a callback on the front of the before
     * chain permanently.
     **/
    public function _setup($callback) 
    {
        $this->_setup_callback = $callback;
    }

    /**
     * This will stick a callback on the front of the before
     * chain permanently.
     **/
    public function _teardown($callback) 
    {
        $this->_teardown_callback = $callback;
    }

    /**
     * The nested context dsl command to create a test.
     **/
    public function it($title, $current_test = null) 
    {
        if ($current_test == null || $this->_describe_chain_is_disabled) {
            $current_test = function ($test) {
                $test->markTestIncomplete('This spec is not implemented yet');
            };
        }
        $this->createIt($title, $current_test);
    }

    protected function createIt($title, $current_test) 
    {
        $this->_args = array();

        $describe_string = implode('::', $this->_describe_title_chain);
        $this->_current_test_name = $describe_string . "::$title";
        $this->_current_test = $current_test;

        $clone = clone $this;
        self::$test_suite[] = $clone;
    }

    public function xit($title, $current_test = null) 
    {
        $current_test = function ($test) {
            $test->markTestIncomplete('This spec is disabled by user');
        };
        $this->createIt($title, $current_test);
    }

    public function when($title, $current_test = null) 
    {
        $this->it('when ' . $title, $current_test);
    }

    public function xwhen($title, $current_test = null) 
    {
        $this->xit('when ' . $title, $current_test);
    }

    /**
     * @see PHPUnit/Framework/TestCase.php for the original implementation.
     * We're overriding it so we can use our _current_test_name variable in
     * place of `name'.
     */
    public function getName($withDataSet = TRUE) 
    {
        if ($withDataSet) {
            return $this->_current_test_name . $this->getDataSetAsString(FALSE);
        } else {
            return $this->_current_test_name;
        }
    }

    /**
     * This is a magic setter method which allows us to define arbitrary attrbitutes
     * on the test itself so we can pass variables between before blocks and the
     * tests themselves (instance variables are used for this in rspec).
     *
     * @example $test->key_value_store = new $test->hash_class($test->initial_values);
     **/
    public function __set($name, $value) 
    {
        $this->_args[$name] = $value;
    }

    /**
     * This is a magic getter method which allows us to retrieve arbitrary attrbitutes
     * on the test itself so we can pass variables between before blocks and the
     * tests themselves (instance variables are used for this in rspec).
     **/
    public function __get($name) 
    {
        return array_key_exists($name, $this->_args) ? $this->_args[$name] : null;
    }

    /**
     * The way that test case implements this is to only have a single test in
     * each instance, so inherently the count will always be 1.
     **/
    public function count()
    {
        return 1;
    }

    /**
     * This runs the actual test, and is intended to be run against the testsuite
     * that is output by the 'suite' function
     **/
    public function run(\PHPUnit_Framework_TestResult $result = NULL) 
    {
        if ($result === NULL) {
            $result = new \PHPUnit_Framework_TestResult;
        }

        $this->_result = $result;
        $this->_result->startTest($this);

        \PHPUnit_Framework_Assert::resetCount();

        \PHP_Timer::start();
        $stop_time = NULL;

        try {

            //Run the before callbacks
            if($this->_setup_callback !== null) {
                call_user_func($this->_setup_callback, $this);
            }
            foreach($this->_before_chain as $current_before) {
                if($current_before !== null) {
                    call_user_func($current_before, $this);
                }
            }

            // Part of the shim to allow us to use PHPUnit's runTest method
            $refClass= new \ReflectionClass( $this);
            for($i = 0; $i < 20; $i++) {
                $refClass = $refClass->getParentClass();
                if($refClass->getName() === 'PHPUnit_Framework_TestCase') {
                    break;
                }
            }
            $refProperty = $refClass->getProperty( 'name' );
            $refProperty->setAccessible(true);
            $refProperty->setValue($this, 'run_current_test');

            $this->runTest();
            $this->verifyMockObjects();

            $this->addToAssertionCount(\PHPUnit_Framework_Assert::getCount());

            if (!$this->assertion_count) {
                $this->markTestIncomplete('This spec does not have any expectation');
            }

            //Run the after callbacks
            if($this->_teardown_callback !== null) {
                call_user_func($this->_teardown_callback, $this);
            }
        }

        catch (\PHPUnit_Framework_AssertionFailedError $e) {
            $stop_time = \PHP_Timer::stop();
            $this->_result->addFailure($this, $e, $stop_time);
        }

        catch (\Esperance\Error $e) {
            $stop_time = \PHP_Timer::stop();
            $this->_result->addFailure($this, new \PHPUnit_Framework_AssertionFailedError($e->getMessage(), $e->getCode(), $e), $stop_time);
        }

        catch (\Exception $e) {
            $stop_time = \PHP_Timer::stop();
            $this->_result->addError($this, $e, $stop_time);
        }

        if ($stop_time === NULL) {
            $stop_time = \PHP_Timer::stop();
        }

        $this->_result->endTest($this, $stop_time);

        return $this->_result;
    }

    /**
     * This is called by the TestUI printer to give a name for the test
     * in the debug output as well as in the failure output in the
     * footer of the test output.
     **/
    public function toString() 
    {
        return $this->_current_test_name;
    }

    /**
     * Adds a value to the assertion counter.
     *
     * @param integer $count
     * @since Method available since Release 3.3.3
     */
    public function addToAssertionCount($count)
    {
        $this->assertion_count += $count;
    }

    /**
     * Returns the number of assertions performed by this test.
     *
     * @return integer
     * @since  Method available since Release 3.3.0
     */
    public function getNumAssertions()
    {
        return $this->assertion_count;
    }

    /**
     * Returns a mock object for the specified class.
     *
     * @param  string  $original_class_name
     * @param  array   $methods
     * @param  array   $arguments
     * @param  string  $mock_class_name
     * @param  boolean $call_original_constructor
     * @param  boolean $call_original_clone
     * @param  boolean $call_autoload
     * @param  boolean $clone_arguments
     * @param  boolean $call_original_methods
     * @return PHPUnit_Framework_MockObject_MockObject
     * @throws PHPUnit_Framework_Exception
     * @since  Method available since Release 3.0.0
     */
    public function getMock($original_class_name, $methods = array(), array $arguments = array(), $mock_class_name = '', $call_original_constructor = TRUE, $call_original_clone = TRUE, $call_autoload = TRUE, $clone_arguments = FALSE, $call_original_methods = FALSE)
    {
        $mock_object = parent::getMock(
            $original_class_name,
            $methods,
            $arguments,
            $mock_class_name,
            $call_original_constructor,
            $call_original_clone,
            $call_autoload,
            $clone_arguments,
            $call_original_methods
        );

        $this->_shadow_mocks[] = $mock_object;

        return $mock_object;
    }

    /**
     * Verifies the mock object expectations.
     *
     * @since Method available since Release 3.5.0
     */
    protected function verifyMockObjects()
    {
        foreach ($this->_shadow_mocks as $mock_object) {
            if ($mock_object->__phpunit_hasMatchers()) {
                $this->assertion_count++;
            }
        }
        $this->_shadow_mocks = array();

        parent::verifyMockObjects();
    }

    /**
     * Default implementation that should be overriden by every test
     */
    public function tests()
    {
    }

}

function describe() 
{
    return \Mirasvit\Rma\Test\Unit\Observer\PHPUsableTest::run_on_current_test('describe', func_get_args());
}

function xdescribe() 
{
    return \Mirasvit\Rma\Test\Unit\Observer\PHPUsableTest::run_on_current_test('xdescribe', func_get_args());
}

function setup() 
{
    return \Mirasvit\Rma\Test\Unit\Observer\PHPUsableTest::run_on_current_test('_setup', func_get_args());
}

function teardown() 
{
    return \Mirasvit\Rma\Test\Unit\Observer\PHPUsableTest::run_on_current_test('_teardown', func_get_args());
}

/**
 * @SuppressWarnings(PHPMD.ShortMethodName)
 */
function it() 
{
    return \Mirasvit\Rma\Test\Unit\Observer\PHPUsableTest::run_on_current_test('it', func_get_args());
}

function xit() 
{
    return \Mirasvit\Rma\Test\Unit\Observer\PHPUsableTest::run_on_current_test('xit', func_get_args());
}

function when() 
{
    return \Mirasvit\Rma\Test\Unit\Observer\PHPUsableTest::run_on_current_test('when', func_get_args());
}

function xwhen() 
{
    return \Mirasvit\Rma\Test\Unit\Observer\PHPUsableTest::run_on_current_test('xwhen', func_get_args());
}

function before() 
{
    return \Mirasvit\Rma\Test\Unit\Observer\PHPUsableTest::run_on_current_test('before', func_get_args());
}
