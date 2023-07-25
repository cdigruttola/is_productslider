/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

window.sliderProduct = (function () {
  return {
    init() {
      const addButton = $('#add-related-product-button');
      const resetButton = $('#reset_related_product');
      const relatedContent = $('#related-content');
      const productItems = $('#slider_product_ids-data');
      const searchProductsBar = $('#slider_product_ids');

      addButton.on('click', (e) => {
        e.preventDefault();
        relatedContent.removeClass('hide');
        addButton.hide();
      });
      resetButton.on('click', (e) => {
        e.preventDefault();
        // eslint-disable-next-line
        modalConfirmation.create(translate_javascripts['Are you sure you want to delete this item?'], null, {
          onContinue: function onContinue() {
            const items = productItems.find('li').toArray();

            items.forEach((item) => {
              console.log(item);
              item.remove();
            });
            searchProductsBar.val('');

            relatedContent.addClass('hide');
            addButton.show();
          },
        }).show();
      });
    },
  };
}());

// eslint-disable-next-line
BOEvent.on('Slider Product Management started', () => {
  sliderProduct.init();
  $('#add-related-product-button').click();
}, 'Back office');
