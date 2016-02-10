/**
 * A generic site tour for KnowPulse navigation.
 */
(function ($) {
  Drupal.behaviors.kpFrontPageTour = {
    attach: function (context, settings) {

    // Override default functionality for any link with the tour-start-link
    // class and instead start the full tour.
    $('a#frontpage-tour-button').live('click', function(event) {
        event.preventDefault();
        // Check if a tour is already going to avoid duplicates
        // OR we could cancel any current tours and start one fresh using: Shepherd.activeTour.cancel();
        if (null == Shepherd.activeTour) {
          startFullTour();
        }
    });

    function startFullTour() {
      // Initialize the tour.
      tour = new Shepherd.Tour({
        defaults: {
          classes: 'shepherd-theme-arrows',
          showCancelLink: true,
        }
      });
      
      // STEP #1: Welcome the user to the tour.
      //-----------------------------------------
      /*
      tour.addStep('frontpage-start', {
        title: 'Welcome!',
        text: 'If you are unsure how to find what you need then take the tour!',
        attachTo: '#header left',
        buttons: [
          {
            text: 'Start Tour',
            action: tour.next,
            classes: 'shepherd-button-primary'
          }
        ]
      });*/

      // STEP #2: Introduce navigation menu if the user is logged in.
      //-----------------------------------------
      tour.addStep('fontpage-2', {
        title: 'Add Content',
        text : 'You can add content including registering sequence data or describing markers, germplasm or projects, here.',
        attachTo : '#block-system-navigation left',
        buttons: [
            {
              text: 'Next',
              action: function() {
                // Move on to the next step.
                tour.next();
                // Scroll to the bottom of the page.
                $('html, body').animate({
                  scrollTop: $("#block-system-navigation").offset().top-100
                }, 1000);
              },
              classes: 'shepherd-button-primary'
            }
          ],
      });

      // STEP #3: Bioinformatics Tools.
      //-----------------------------------------
      tour.addStep('fontpage-3', { 
        title: 'Bioinformatic Tools',
        text : 'If you have a sequence and need to find its homologous entry in KnowPulse, you can use the BLAST. Alternatively, if you would like to graphically explore the crop genomes, you can use the corresponding JBrowse.',
        attachTo : '#block-menu-menu-bioinformatics-resources left',
        buttons: Drupal.kptheme.shepherd.scrollingButtons('#block-system-navigation', '#block-menu-menu-genomic-data'),
      });

      // STEP #4: Genomic Data.
      //-----------------------------------------
      tour.addStep('fontpage-4', {
        title: 'Genomic Data',
        text : 'If you are looking for a particular sequence, variant or marker by name you can use the corresponding searches in this menu. To search for markers that are polymorphic in specific germplasm, use the genotype searches.',
        attachTo : '#block-menu-menu-genomic-data left',
        buttons: Drupal.kptheme.shepherd.scrollingButtons('#block-menu-menu-bioinformatics-resources', '#block-menu-menu-germplasm'),
      });

      // STEP #5: Germplasm data (if the user has permission).
      //-----------------------------------------
      tour.addStep('fontpage-5', {
        title: 'Germplasm',
        text : 'If you would like more information about a particular variety, accession or cross then select the crop to access a general search. More specific searches will be exposed in this menu once you select the crop.',
        attachTo : '#block-menu-menu-germplasm left',
        buttons: Drupal.kptheme.shepherd.scrollingButtons('#block-menu-menu-genomic-data', '#block-menu-menu-our-research'),
      });
      
      // STEP #6: Our Research.
      //-----------------------------------------
      tour.addStep('fontpage-6', {
        title: 'Our Research',
        text : 'If you would like more information about the University of Saskatchewan Pulse group, you can access our projects, publications and general crop/species information in this menu.',
        attachTo : '#block-menu-menu-our-research left',
        buttons: Drupal.kptheme.shepherd.scrollingButtons('#block-menu-menu-germplasm', '#frontpage-tour-button', true),
      });
      
      // Now actually start the tour
      tour.start();
    }
  }};
}(jQuery));
