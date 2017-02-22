# features/user.feature
Feature: User
  In order to create users
  As a OroCRM Admin user
  I need to be able to open Create User dialog and create new user

  Scenario: Create new user
    Given I login as administrator
    And go to System/User Management/Users
    And press "Create User"
    When I save and close form
    Then I should see validation errors:
      | Enabled       | This value should not be null.  |
      | Username      | This value should not be blank. |
      | Password      | This value should not be blank. |
      | First Name    | This value should not be blank. |
      | Last Name     | This value should not be blank. |
      | Primary Email | This value should not be blank. |
    When I fill "User Form" with:
          | Username          | userName       |
          | Password          | Pa$$w0rd       |
          | Re-Enter Password | Pa$$w0rd       |
          | First Name        | First Name     |
          | Last Name         | Last Name      |
          | Primary Email     | email@test.com |
          | Roles             | Administrator  |
          | Enabled           | Enabled        |
    And I save and close form
    Then I should see "User saved" flash message
