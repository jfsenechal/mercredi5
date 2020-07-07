Feature: Gestion des plaines
  Je suis connecté
  Ajout une plaine
  Modifier une plaine
  Modifier les jours de la plaine
  Supprimer une plaine

  Background:
    Given I am logged in as an admin
    Given I am on "/admin/plaine/"
    Then I should see "Liste des plaines"

  Scenario: Ajout une plaine
    Then I follow "Ajouter une plaine"
    And I fill in "plaine[nom]" with "Carnaval 2020"
    And I fill in "plaine[prix1]" with "8"
    And I fill in "plaine[prix1]" with "5"
    And I fill in "plaine[plaine_groupes][0][inscription_maximum]" with "20"
    And I fill in "plaine[plaine_groupes][1][inscription_maximum]" with "15"
    And I fill in "plaine[plaine_groupes][2][inscription_maximum]" with "12"
    And I press "Sauvegarder"
    Then I should see "Dates pour Carnaval 2020"
    Then I fill in "plaine_jour[jours][0][date_jour]" with "2020-02-10"
    Then I fill in "plaine_jour[jours][1][date_jour]" with "2020-02-11"
    And I press "Sauvegarder"
    Then I should see "les dates ont bien été enregistrées"
    Then I should see "lundi 10 février 2020"
    Then I should see "mardi 11 février 2020"

  Scenario: Modifier une plaine
    Then I follow "Plaine de noel"
    Then I follow "Modifier"
    And I fill in "plaine[prix1]" with "6"
    And I fill in "plaine[plaine_groupes][1][inscription_maximum]" with "22"
    And I press "Sauvegarder"
    Then I should see "6 €"
    Then I should see "22"

  Scenario: Modifier les jours de la plaine
    Then I follow "Plaine de noel"
    Then I follow "Jours"
  #  Then I follow "Ajouter une date"
  #  And I fill in "plaine_jour[jours][3][date_jour]" with "2020-12-24"
    And I press "Sauvegarder"
    Then I should see "mercredi 23 décembre"

  Scenario: Supprimer une plaine
    Then I follow "Plaine de noel"
    Then I press "Supprimer la plaine"
   # Then print last response
    Then I should see "La plaine a bien été supprimée"
