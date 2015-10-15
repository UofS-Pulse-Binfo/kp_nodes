# KnowPulse Nodes
Contains KnowPulse-specific Themeing of node pages. 

**NOTE: This is a module rather than a theme to ensure that you don't need the base theme (KP Theme & Antonelli) to get this functionality. You should ensure that the weight of this module is lighter (ie: -2) than all the Tripal modules in order to ensure the variables are built before this module attempts to manipulate them.**

## Chado Feature
* Adds a side banner to the top right hand corner of feature pages specifying the type of feature (ie: marker, variant, sequence).
* Customizes the Overview pane depending upon the type of feature. For example, if the type is marker, then the subtype is stored in the feature proeprties (ie: KASPar marker) and we would like to link to the source variant.

## Chado Organism
* Customizes the Overview pane to include a drupal image field (additional functionality of stating the source of the image).
* Renames the organism feature counts pane to 'Sequence & Variant Data' and customizes the template to show a views-generated pie chart describing the types and proportion of features for this organism, as well as, providing a table of feature counts linking to pre-filtered listings of content.
* Renames the organism stocks pane to 'Germplasm Data' and customizes the template to show a views-generated pie chart describing the types and proportion of stocks/germplasm for this organism, as well as, providing a table of stock counts.
* Customizes the organism teaser to include the drupal image file and a subset of the description.
* Removes the feature browser.

## Chado Project
* Customizes the Project overview to include the date and long description.
* Change the relationships pane to 'Related Projects' and customize the template to display sub-projects and parent projects as bulleted lists rather than grouping all relationships into a single table.

## UofS Research Area
Tells Drupal to use the tripal generic node template for nodes of type 'uofs_research_area'. Furthermore, provides instructions that the overview should consist of the body and thumbnail fields.
