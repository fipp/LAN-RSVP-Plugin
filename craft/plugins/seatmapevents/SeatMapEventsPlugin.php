<?php
namespace Craft;

class SeatMapEventsPlugin extends BasePlugin {
  function getDescription() {
    return 'Lets you create events with an optional seatmap that users can sign up to.';
  }

  function getDeveloper() {
    return 'Terje Ness Andersen';
  }

  function getDeveloperUrl() {
    return 'http://terjeandersen.net';
  }

  function getDocumentationUrl() {
    return 'http://terjeandersen.net/craft-seatmap-events';
  }

  function getName() {
    return Craft::t('Seat Map Events')
  }

  function getVersion() {
    return '1.0';
  }
}
