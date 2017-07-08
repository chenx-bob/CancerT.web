// JavaScript Document


/**
 * @author Daniel Carbone (daniel.p.carbone@vanderbilt.edu)
 * @version {datemodified}
 */

var ExpandableNavigationViewCollection = Backbone.View.extend({
    "className" : "content-navigation",
    "tagName" : "ul",

    events : {
        "click li" : "toggleChildrenLI",
        "click li .expandable" : "toggleChildren"
    },

    /**
     * Event listener for when user clicks on li
     *
     * @param  event jQuery Event Object
     * @return void
     */
    toggleChildrenLI : function(event) {
        var $link = $(event.target).children(".expandable:eq(0)");
        // This conditional ensures we only fire expand
        // logic if this element has a submenu in it
        if ($link.size() > 0)
        {
            event.stopPropagation();
            event.preventDefault();
            this._toggleChild($link);
        }
    },
    /**
     * Event listener when user clicks on a.expandable
     *
     * @param  event jQuery Event Object
     * @return void
     */
    toggleChildren : function(event) {
        event.preventDefault();
        event.stopPropagation();
        this._toggleChild($(event.target));
    },
    /**
     * Event method that triggers child menu expand and collapse methods
     *
     * @access private
     * @param  $link  anchor tag associated with event
     * @return void
     */
    _toggleChild : function($link) {
        _.each(this.options.childMenus, function(menu) {
            if (menu.prev().is($link))
                menu.expand();
            else
                menu.collapse()
        });
    }
});
/**
 * @author Daniel Carbone (daniel.p.carbone@vanderbilt.edu)
 * @version {datemodified}
 */

var TabNavigation = GAView.extend({
    events : {
        "click .section-navigation" : "changeTab"
    },
    changeTab : function(event) {
        event.stopPropagation();
        var $target = $(event.currentTarget);
        if ($target.hasClass("active"))
        {
            return false;
        }
        if (this.options.track_events === true)
        {
            this._ga_trackEvent("TabNav", "Changed", $target.text());
        }

        var index = $target.index();

        $target.addClass('active').siblings(".section-navigation").removeClass("active");

        $(this.options.content_container)
            .children(".section-content").removeClass("active")
            .end().children(".section-content:eq("+index+")").addClass("active");
    }
});
/**
 * @author Daniel Carbone (daniel.p.carbone@vanderbilt.edu)
 * @version {datemodified}
 */

var ExpandableNavigationView = GAView.extend({
    "className" : "submenu",
    "tagName" : "ul",
    /**
     * Helper method to return <a /> tag before
     * this ul.submenu
     *
     * @return jQueryElement
     */
    prev : function()
    {
        return $($(this.el).prev());
    },
    /**
     * Runs Expand logic
     *
     * @return void
     */
    expand : function()
    {
        var _this = this;
        this.$el.animate({height : "show"}, 250, function() {
            $(this).removeClass('closed').addClass("expanded");
            _this._fireEvent(_this.$el.prev(".expandable"));
        });
        this.$el.prev(".expandable").addClass("expanded");
    },
    /**
     * Runs collapse logic
     *
     * @return void
     */
    collapse : function()
    {
        this.$el.animate({height : "hide"}, 250, function() {
            $(this).removeClass("expanded").addClass('closed');
        });
        this.$el.prev(".expandable").removeClass("expanded");
    },
    /**
     * Fires GAQ event
     *
     * @access private
     * @return void
     */
    _fireEvent : function($link)
    {
        this._ga_trackEvent("ExpandableNav", "Changed", $link.text());
    }
});

$(document).ready(function($) {

    var section_nav = new TabNavigation({el: document.getElementById("section-navigation-container")});
    section_nav.options = {
        content_container: document.getElementById("section-content-container"),
        track_events: true
    };

    $("#content-navigation").children("div.expandable-navigation-container").each(function () {
        var expandable_nav = [];
        $(this).find("ul.submenu").each(function () {
            expandable_nav.push(new ExpandableNavigationView({
                el: this
            }));
        });
        var leftNav = new ExpandableNavigationViewCollection({
            el: $(this).children("ul")
        });

        leftNav.options = {};
        leftNav.options.childMenus = expandable_nav;
    });

});