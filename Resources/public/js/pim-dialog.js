/**
 * PimDialog class purposes an easier way to call ModalDialog components
 * 
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * 
 * @uses Backbone.BootstrapModal
 * 
 * Example:
 *      PimDialog.alert('{{ 'MyMessage' | trans }}', 'MyTitle');
 */
var PimDialog = new function() {
    /**
     * Open a modal dialog without cancel button
     * @param string content
     * @param string title
     */
    this.alert = function(content, title) {
        if (!_.isUndefined(Backbone.BootstrapModal)) {
            var alert = new Backbone.BootstrapModal({
                allowCancel: false,
                title: title,
                content: content
            });
            alert.open();
        } else {
            window.alert(content);
        }
    },
    
    /**
     * Open a confirm modal dialog to validate the action made by user
     * If user validate its action, a js callback function is called
     * @param string content
     * @param string title
     * @param function callback
     */
    this.confirm = function(content, title, callback) {
        if (!_.isUndefined(Backbone.BootstrapModal)) {
            var confirm = new Backbone.BootstrapModal({
                title: title,
                content: content
            });
            confirm.on('ok', callback);
            confirm.open();
        } else {
            callback();
        }
    }
}
