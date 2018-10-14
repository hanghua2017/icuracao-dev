define([
    'jquery',
    'jquery/ui',
    'Xtento_ProductExport/js/jquery/tree.jquery'
], function ($) {
    $(document).ready(function () {
        var $tree = $('#category_tree');
        var checkInterval = setInterval(function () {
            // Wait for jquery.tree
            if (typeof $tree.tree !== 'undefined') {
                clearInterval(checkInterval);

                // Begin init once tree library is ready
                $tree.tree({
                    data: window.xtentoCategoryTree,
                    autoOpen: true,
                    keyboardSupport: false,
                    selectable: false,
                    onCreateLi: function (node, $li) {
                        $li.find('.jqtree-element').append(
                            '<div class="category-input-container"><div class="mage-suggest"><div class="mage-suggest-inner"><input type="text" class="category-input" id="category-' + node.id + '" data-category-id="' + node.id + '" value="' + node.mappedValue + '"/></div></div></div>'
                        );
                    }
                });

                // Once category mapping is updated - store it in our data field
                var updateCategoryMapping = function(element) {
                    window.xtentoMappedCategories[$(element).data('category-id')] = $(element).val();
                };
                // Init mapping
                $('.category-input').each(function() {
                    updateCategoryMapping(this);
                });
                $('#category_mapping').val(JSON.stringify(window.xtentoMappedCategories));
                $('#category_mapping').attr('name', 'category_mapping');
                // Listener for category mapping updates
                $('.category-input').change(function () {
                    updateCategoryMapping(this);
                    $('#category_mapping').val(JSON.stringify(window.xtentoMappedCategories));
                });

                // Init autocomplete
                var updateAutoComplete = function () {
                    $('.category-input').autocomplete({
                        source: window.xtentoTaxonomyUrl + '?source=' + $('#taxonomy_source').val(),
                        minLength: 2,
                        messages: {
                            noResults: '',
                            results: function () {
                            }
                        },
                        select: function (event, ui) {
                            //event.target.name
                            //console.log(ui.item.label);
                        }
                    });
                };
                updateAutoComplete();

                $('#taxonomy_source').change(function () {
                    if ($(this).val() !== '') {
                        // Update for new taxonomy source
                        updateAutoComplete();
                    } else {
                        // Disable autocomplete for "No taxonomy"
                        $('.category-input').autocomplete({minLength: 999});
                    }
                });
            }
        }, 250);
    });
});