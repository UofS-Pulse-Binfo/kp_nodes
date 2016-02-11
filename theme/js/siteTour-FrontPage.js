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
      
      // First define the selectors for my tour.
      // We do this here so we can determine which steps exist.
      var steps = [
        {
          selector: '#block-system-navigation',
          present: $('#block-system-navigation').length
        },
        {
          selector: '#block-menu-menu-bioinformatics-resources',
          present: $('#block-menu-menu-bioinformatics-resources').length
        },
        {
          selector: '#block-menu-menu-genomic-data',
          present: $('#block-menu-menu-genomic-data').length
        },
        {
          selector: '#block-menu-menu-germplasm',
          present: $('#block-menu-menu-germplasm').length
        },
        {
          selector: '#block-menu-menu-our-research',
          present: $('#block-menu-menu-our-research').length
        },
        {
          selector: '#block-user-login',
          present: $('#block-user-login').length
        }
      ];
      
      // Define a function to get the selector of the next step that is present.
      var getNextSelector = function(currentIndex) {
        checkIndex = currentIndex+1;
        while (checkIndex in steps) {
          if (steps[checkIndex].present) {
            return steps[checkIndex].selector;
          }
          checkIndex++;
        }
        return '#frontpage-tour-button';
      }
      
      // Define a function to determine if this is the last step.
      var isLast = function(currentIndex) {
        console.log('checking '+currentIndex);
        checkIndex = currentIndex+1;
        while (checkIndex in steps) {
          if (steps[checkIndex].present) {
            return false;
          }
          checkIndex++;
        }
        return true;
      }

      // STEP #1: Introduce navigation menu if the user is logged in.
      //-----------------------------------------
      var last = null;
      if (steps[0].present) {
        tour.addStep('fontpage-1', {
          title: 'Add Content',
          text : 'You can add content including registering sequence data or describing markers, germplasm or projects, here.',
          attachTo : steps[0].selector + ' left',
          buttons: Drupal.kptheme.shepherd.scrollingButtons(last, getNextSelector(0), isLast(0)),
        });
        last = steps[0].selector;
      }
      
      // STEP #2: Bioinformatics Tools.
      //-----------------------------------------
      if (steps[1].present) {
        tour.addStep('fontpage-2', { 
          title: 'Bioinformatic Tools',
          text : 'If you have a sequence and need to find its homologous entry in KnowPulse, you can use the BLAST. Alternatively, if you would like to graphically explore the crop genomes, you can use the corresponding JBrowse.',
          attachTo : steps[1].selector + ' left',
          buttons: Drupal.kptheme.shepherd.scrollingButtons(last, getNextSelector(1), isLast(1)),
        });
        last = steps[1].selector;
      }

      // STEP #3: Genomic Data.
      //-----------------------------------------
      if (steps[2].present) {
        tour.addStep('fontpage-3', {
          title: 'Genomic Data',
          text : 'If you are looking for a particular sequence, variant or marker by name you can use the corresponding searches in this menu. To search for markers that are polymorphic in specific germplasm, use the genotype searches.',
          attachTo : steps[2].selector + ' left',
          buttons: Drupal.kptheme.shepherd.scrollingButtons(last, getNextSelector(2), isLast(2)),
        });
        last = steps[2].selector;
      }
      
      // STEP #4: Germplasm data (if the user has permission).
      //-----------------------------------------
      if (steps[3].present) {
        tour.addStep('fontpage-4', {
          title: 'Germplasm',
          text : 'If you would like more information about a particular variety, accession or cross then select the crop to access a general search. More specific searches will be exposed in this menu once you select the crop.',
          attachTo : steps[3].selector + ' left',
          buttons: Drupal.kptheme.shepherd.scrollingButtons(last, getNextSelector(3), isLast(3)),
        });
        last = steps[3].selector;
      }
            
      // STEP #5: Our Research.
      //-----------------------------------------
      if (steps[4].present) {
        tour.addStep('fontpage-5', {
          title: 'Our Research',
          text : 'If you would like more information about the University of Saskatchewan Pulse group, you can access our projects, publications and general crop/species information in this menu.',
          attachTo : steps[4].selector + ' left',
          buttons: Drupal.kptheme.shepherd.scrollingButtons(last, getNextSelector(4), isLast(4)),
        });
        last = steps[4].selector;
      }

      // STEP #6: Login
      //-----------------------------------------
      if (steps[5].present) {
        tour.addStep('fontpage-5', {
          title: 'User Login',
          text : 'If you would like to access the Lentil genome, add content or retrieve information about our germplasm, you will need to login.</p><p>Additionally your account will need authorization. For the Lentil Genome this can be obtained by <a href="http://knowpulse.usask.ca/portal/lentil-genome">clicking here</a>; whereas, to add content or browse our germplasm you will need to talk to Lacey Sanderson or Kirstin Bett directly.',
          attachTo : steps[5].selector + ' left',
          buttons: Drupal.kptheme.shepherd.scrollingButtons(last, getNextSelector(5), isLast(5)),
        });
      }
         
      // Now actually start the tour
      tour.start();
    }
  }};
}(jQuery));
