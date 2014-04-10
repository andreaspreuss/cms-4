do($ = jQuery) ->
  $(document).ready ->
    $('pre > code').parent().addClass('prettyprint')
    prettyPrint()
    $('article table').addClass('table table-striped table-bordered')
