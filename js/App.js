var React = require('react'),
    DOM = React.DOM, div = DOM.div, button = DOM.button, ul = DOM.ul, li = DOM.li

// This is just a simple example of a component that can be rendered on both
// the server and browser

var default_export = React.createClass({displayName: "default_export",

  // We initialise its state by using the `props` that were passed in when it
  // was first rendered. We also want the button to be disabled until the
  // component has fully mounted on the DOM
  getInitialState: function() {
    return {items: this.props.items, disabled: true}
  },

  // Once the component has been mounted, we can enable the button
  componentDidMount: function() {
    this.setState({disabled: false})
  },

  // Then we just update the state whenever its clicked by adding a new item to
  // the list - but you could imagine this being updated with the results of
  // AJAX calls, etc
  handleClick: function() {
    this.setState({
      items: this.state.items.concat('Item ' + this.state.items.length)
    })
  },

  // For ease of illustration, we just use the React JS methods directly
  // (no JSX compilation needed)
  // Note that we allow the button to be disabled initially, and then enable it
  // when everything has loaded
  render: function() {

    return div(null,

      button({onClick: this.handleClick, disabled: this.state.disabled}, 'Add Item'),

      ul({children: this.state.items.map(function(item) {
        return li(null, item)
      })})

    )
  },
})

// tutorial3.js
var StreamBox = React.createClass({displayName: "StreamBox",
  getInitialState: function() {
    // called server-side
    return {strim_list: this.props.strim_list, disabled: true};
  },
  componentDidMount: function () {
    // called on client side?
    // this.setState({disabled: false})
    // it's fine to use jquery here
    $(document).on('strim_list', function (e, strim_list) {
      this.setState({strim_list: strim_list})
    }.bind(this));
    // todo: hook into state change
    // $.ajax({
    //   url: this.props.url,
    //   dataType: 'json',
    //   success: function(data) {
    //     this.setState({data: data});
    //   }.bind(this),
    //   error: function(xhr, status, err) {
    //     console.error(this.props.url, status, err.toString());
    //   }.bind(this)
    // });
  },
  render: function() {
    return (
      React.createElement("div", {className: "streamBox"}, 
        React.createElement(StreamList, {strim_list: this.state.strim_list})
      )
    );
  }
});

var StreamList = React.createClass({displayName: "StreamList",
  // getInitialState: function () {
  //   return {strim_list: this.props.strim_list, disabled: true};
  // },
  render: function() {
    var cx = React.addons.classSet;
    var list = this.props.strim_list || [];
    console.log('rendering stream list', list.length, "long list")
    var streamNodes = list.map(function (stream) {
      // config the name/title/label
      stream.metadata.label = stream.metadata.channel+" on "+stream.metadata.platform
      if(stream.metadata.hasOwnProperty('name') && stream.metadata.name.length > 0){
        stream.metadata.label = stream.metadata.name+"\'s channel"
      }

      // config the badge/view counter
      var classes = cx({
        'pull-right label label-as-badge': true,
        'label-success': stream.metadata['live'],
        'label-danger': !stream.metadata['live']
      });

      var visibility = stream.metadata['live'] ? 'visible' : 'hidden' 

      return (
        React.createElement(Stream, {key: stream.metadata.url, metadata: stream.metadata, live_class: classes, visibility: visibility})
      );
    });
    var allNodes = []
    streamNodes.forEach(function(s){
      allNodes.push(s)
      allNodes.push(React.createElement("div", {className: "clear"}))
    })
    return (
      React.createElement("div", {className: "streamList row stream-list"}, 
        allNodes
      )
    );
  }
});

// markdown processor

var Stream = React.createClass({displayName: "Stream",
  render: function() {
    return (
      React.createElement("div", {className: "stream col-xs-12 col-sm-4 col-md-3 col-lg-2"}, 
        React.createElement("div", {className: "thumbnail"}, 
          React.createElement("a", {href: this.props.metadata.url, className: this.props.visibility}, 
            React.createElement("img", {className: "stream-thumbnail", src: this.props.metadata.image_url, alt: this.props.metadata.label})
          ), 
          React.createElement("div", {className: "caption"}, 
            React.createElement("div", null, 
              React.createElement("a", {href: this.props.metadata.url}, " ", this.props.metadata.label), 
              React.createElement("span", {className: this.props.live_class}, 
                this.props.metadata.rustlers, " ", React.createElement("span", {className: "glyphicon glyphicon-user", "aria-hidden": "true"})
              )
            )
          )
        )
      )
    );
  }
});


// module.exports = default_export
module.exports = StreamBox;

// var strim_list = [];

// React.render(
//   <StreamBox strim_list={strim_list} />,
//   document.getElementById('strims')
// );
