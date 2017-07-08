jQuery(document).ready(function($) {

    'use strict';

    var $form = $('#TumorContentSearch');
    var $TissueDropdown = $('#TissueDropdown');
    var $TumorDropdown = $('#TumorDropdown');
    var $GeneDropdown = $('#GeneDropdown');
	var dJax = null;
    var gJax = null;
    // First do some normalization and load Tissue Type
	$TissueDropdown.prop('disabled', true).children('option:gt(0)').remove();
	$TissueDropdown.children('option:first-child').text('Loading Tissue...');

	$TumorDropdown.prop('disabled', true).children('option:gt(0)').remove();
	$TumorDropdown.children('option:first-child').text('Select Tissue First');
	
	$GeneDropdown.prop('disabled', true).children('option:gt(0)').remove();
	$GeneDropdown.children('option:first-child').text('Select Tissue First');
	
	dJax = $.ajax({
		url : base_url('lib/get_info_for_tissue.php', false, false, false, {type : 'tissue_type'}),
		dataType : 'json'
	})
	.done(function(data, textStatus, jqXHR) {
		_.each(data, function(id, name) {
			 $TissueDropdown.append('<option value="' + id + '">' + name + '</option>');
		});
		$TissueDropdown.prop('disabled', false).children('option:first-child').text('Select Tissue');
		$TumorDropdown.children('option:first-child').text('Select Tissue First');
		$GeneDropdown.children('option:first-child').text('Select Tissue First');
	});
    var tissue_name = null;
    var tumor_name = null;

    // Setup form-level event listeners
    $form.on('submit', function(event) {
        event.stopPropagation();
        event.preventDefault();

        if (tissue_name !== null) {
            var uri = 'search.php';
            var params = {};
			params['searchtype'] = 'tissue';
			params['tissue'] = tissue_name;
            if (tumor_name !== null) {
				params['tumor'] = tumor_name;
                if (typeof $GeneDropdown.val() === 'string' && $GeneDropdown.val() !== ''){
                    params['gene'] = $GeneDropdown.val();
                }

            }
            //var referrer = $('#referrer');

            //if(referrer.length > 0){
                //params['ref'] = referrer.val();
            //}

            window.location = base_url(uri, false, false, false, params);
        }
    });
	
	// Populate Tumor Dropdown based on Tissue Dropdown choice
    $TissueDropdown.on('change', function(event) {

        if (dJax !== null) {
            dJax.abort();
        }

        if (gJax !== null){
            gJax.abort();
        }

        var val = $(this).val();

        if (val === '') {
            tumor_name = null;
            tissue_name = null;

            event.stopPropagation();
            event.preventDefault();

            // Remove any results
            $TumorDropdown.prop('disabled', true).children('option:gt(0)').remove();
            $TumorDropdown.children('option:first-child').text('Select Tissue First');

            $GeneDropdown.prop('disabled', true).children('option:gt(0)').remove();
            $GeneDropdown.children('option:first-child').text('Select Tissue First');

            return false
        }

        $TumorDropdown.prop('disabled', true).children('option:gt(0)').remove();
        $TumorDropdown.children('option:first-child').text('Loading Tumor for ' + $TissueDropdown.children('option:selected').text());

        $GeneDropdown.prop('disabled', true).children('option:gt(0)').remove();
        $GeneDropdown.children('option:first-child').text('Select Tissue First');
		
		tissue_name = $TissueDropdown.children('option:selected').text();
		tumor_name = null;

        dJax = $.ajax({
            url : base_url('lib/get_info_for_tissue.php', false, false, false, {type : 'tumor_type',tissue : val}),
            dataType : 'json'
        })
        .done(function(data, textStatus, jqXHR) {
            _.each(data, function(id, name) {
                 $TumorDropdown.append('<option value="' + id + '">' + name + '</option>');
            });

            $TumorDropdown.prop('disabled', false).children('option:first-child').text('Select Tumor');

            $GeneDropdown.children('option:first-child').text('Select Tumor Next');
        });

    });

    // Populate Gene Dropdown based on Tissue & Tumor selection
    $TumorDropdown.on('change', function(event) {

        if (gJax !== null){
            gJax.abort();
        }

        var val = $(this).val();

        if (val === '') {
            tumor_name = null;

            event.stopPropagation();
            event.preventDefault();

            // Remove any results
            $GeneDropdown.prop('disabled', true).children('option:gt(0)').remove();
            $GeneDropdown.children('option:first-child').text('Select Tumor Next');

            return false
        }

        tumor_name = $(this).children('option:selected').text();

        $GeneDropdown.prop('disabled', true).children('option:gt(0)').remove();
        $GeneDropdown.children('option:first-child').text('Loading Gene...');

        gJax = $.ajax({
                url : base_url(
                    'lib/get_info_for_tissue.php',
                    false,
                    false,
                    false,
                    {type : 'gene_type', tissue: tissue_name, tumor : val}),
                dataType : 'json'
            })
            .done(function(data, textStatus, jqXHR) {
                _.each(data, function(id, name) {
                    $GeneDropdown.append('<option value="'+id+'">'+name+'</option>');
                });

                $GeneDropdown.prop('disabled', false).children('option:first-child').text('Select Gene');
            });
    });
	
	
	
	var $form1 = $('#GeneContentSearch');
    var $GenesDropdown = $('#GenesDropdown');
    var $TissueDropdown1 = $('#TissueDropdown1');
    var $VariantDropdown = $('#VariantDropdown');
	var dJax = null;
    var gJax = null;
    // First do some normalization and load Tissue Type
	$GenesDropdown.prop('disabled', true).children('option:gt(0)').remove();
	$GenesDropdown.children('option:first-child').text('Loading Gene...');

	$TissueDropdown1.prop('disabled', true).children('option:gt(0)').remove();
	$TissueDropdown1.children('option:first-child').text('Select Gene First');
	
	$VariantDropdown.prop('disabled', true).children('option:gt(0)').remove();
	$VariantDropdown.children('option:first-child').text('Select Gene First');
	
	dJax = $.ajax({
		url : base_url('lib/get_info_for_gene.php', false, false, false, {type : 'gene_type'}),
		dataType : 'json'
	})
	.done(function(data, textStatus, jqXHR) {
		_.each(data, function(id, name) {
			 $GenesDropdown.append('<option value="' + id + '">' + name + '</option>');
		});
		$GenesDropdown.prop('disabled', false).children('option:first-child').text('Select Gene');
		$TissueDropdown1.children('option:first-child').text('Select Gene First');
		$VariantDropdown.children('option:first-child').text('Select Gene First');
	});
    var gene_name = null;
    var variant_name = null;

    // Setup form-level event listeners
    $form1.on('submit', function(event) {
        event.stopPropagation();
        event.preventDefault();

        if (gene_name !== null) {
            var uri = 'search.php';
            var params = {};

            if (variant_name !== null) {
				params['searchtype'] = 'case';
				params['gene']  = gene_name;
				params['variant'] = encodeURIComponent(variant_name);

                if (typeof $TissueDropdown1.val() === 'string' && $TissueDropdown1.val() !== ''){
                    params['tissues'] = $TissueDropdown1.val();
                }
				var referrer = $('#referrer');

            	if(referrer.length > 0){
                	params['ref'] = referrer.val();
            	}

            	window.location = base_url(uri, false, false, false, params);
			}
			window.location = base_url(uri, false, false, false, params);
		}
    });
	
	// Populate Type Dropdown based on Gene Dropdown choice
    $GenesDropdown.on('change', function(event) {

        if (dJax !== null) {
            dJax.abort();
        }

        if (gJax !== null){
            gJax.abort();
        }

        var val = $(this).val();

        if (val === '') {
            gene_name = null;
            variant_name = null;

            event.stopPropagation();
            event.preventDefault();

            // Remove any results
            $TissueDropdown1.prop('disabled', true).children('option:gt(0)').remove();
            $TissueDropdown1.children('option:first-child').text('Select Gene First');

            $VariantDropdown.prop('disabled', true).children('option:gt(0)').remove();
            $VariantDropdown.children('option:first-child').text('Select Gene First');

            return false
        }

        $VariantDropdown.prop('disabled', true).children('option:gt(0)').remove();
        $VariantDropdown.children('option:first-child').text('Loading Variant for ' + $GenesDropdown.children('option:selected').text());

        $TissueDropdown1.prop('disabled', true).children('option:gt(0)').remove();
        $TissueDropdown1.children('option:first-child').text('Select Gene First');
		
		gene_name = $GenesDropdown.children('option:selected').text();
		variant_name = null;

        dJax = $.ajax({
            url : base_url('lib/get_info_for_gene.php', false, false, false, {type : 'variant_type',gene_type : val}),
            dataType : 'json'
        })
        .done(function(data, textStatus, jqXHR) {
            _.each(data, function(id, name) {
				if (id !== '') 
                 $VariantDropdown.append('<option value="' + id + '">' + name + '</option>');
            });
            $VariantDropdown.prop('disabled', false).children('option:first-child').text('Select Variant');
            $TissueDropdown1.children('option:first-child').text('Select Variant Next');
			if($VariantDropdown.children('option').size() === 1){
				$VariantDropdown.prop('disabled', true).children('option:gt(0)').remove();
        		$VariantDropdown.children('option:first-child').text('No Variant Information');

        		$TissueDropdown1.prop('disabled', true).children('option:gt(0)').remove();
        		$TissueDropdown1.children('option:first-child').text('No Variant Information');
			}
        });

    });

    // Populate Variant Dropdown based on Gene & Type selection
    $VariantDropdown.on('change', function(event) {

        if (gJax !== null){
            gJax.abort();
        }

        var val = $(this).val();

        if (val === '') {
            variant_name = null;

            event.stopPropagation();
            event.preventDefault();

            // Remove any results
            $TissueDropdown1.prop('disabled', true).children('option:gt(0)').remove();
            $TissueDropdown1.children('option:first-child').text('Select Varaint Next');

            return false
        }

        variant_name = $(this).children('option:selected').val();

        $TissueDropdown1.prop('disabled', true).children('option:gt(0)').remove();
        $TissueDropdown1.children('option:first-child').text('Loading Tissue...');

        gJax = $.ajax({
                url : base_url(
                    'lib/get_info_for_gene.php',
                    false,
                    false,
                    false,
                    {type : 'tissue_type'}),
                dataType : 'json'
            })
            .done(function(data, textStatus, jqXHR) {
                _.each(data, function(id, name) {
					if (id !== '')
                    $TissueDropdown1.append('<option value="'+id+'">'+name+'</option>');
                });

                $TissueDropdown1.prop('disabled', false).children('option:first-child').text('Select Tissue');
				if($TissueDropdown1.children('option').size() === 1){
					$TissueDropdown1.prop('disabled', true).children('option:gt(0)').remove();
					$TissueDropdown1.children('option:first-child').text('No Tissue');
				}
            });
    });

});