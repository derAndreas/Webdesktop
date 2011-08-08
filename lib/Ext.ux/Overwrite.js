/**
 * CUSTOMER OVERWRITES TO MODULES
 *
 * Includes:
 *  - Ext.tab.Bar: If close a tab, setActive to the previusly one opened and not to the next or first tab
 */


Ext.override(Ext.tab.Bar, {
    /**
     * @private
     * Marks the given tab as active
     * @param {Ext.Tab} tab The tab to mark active
     */
    setActiveTab: function(tab) {
        if (tab.disabled) {
            return;
        }
        var me = this;
        if (me.activeTab) {
            me.previousTab = me.activeTab;
            me.activeTab.deactivate();
        }
        tab.activate();

        if (me.rendered) {
            me.layout.layout();
            tab.el.scrollIntoView(me.layout.getRenderTarget());
        }
        me.activeTab = tab;
        me.fireEvent('change', me, tab, tab.card);
    },

    /**
     * @private
     * Closes the given tab by removing it from the TabBar and removing the corresponding card from the TabPanel
     * @param {Ext.Tab} tab The tab to close
     */
    closeTab: function(tab) {
        var me = this,
            card = tab.card,
            tabPanel = me.tabPanel,
            nextTab;
            
        if (card && card.fireEvent('beforeclose', card) === false) {
            return false;
        }

        if (tab.active && me.items.getCount() > 1) {
            nextTab = me.previousTab || tab.next('tab') || me.items.items[0];
            me.setActiveTab(nextTab);
            if (tabPanel) {
                tabPanel.setActiveTab(nextTab.card);
            }
        }
        /*
         * force the close event to fire. By the time this function returns,
         * the tab is already destroyed and all listeners have been purged
         * so the tab can't fire itself.
         */
        tab.fireClose();
        me.remove(tab);

        if (tabPanel && card) {
            card.fireEvent('close', card);
            tabPanel.remove(card);
        }
        
        if (nextTab) {
            nextTab.focus();
        }
    }
});

