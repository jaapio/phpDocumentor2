Feature: Phpdocumentor is able to process method and function arguments and their types

  Scenario: A function with a single argument and native php type
    Given A single file named "test.php" based on "syntax/function_arguments.php"
    When I run "phpdoc --force -f test.php"
    Then the application must have run successfully
    And a function named "\singleArgument()" must exist with argument $foo of type int
