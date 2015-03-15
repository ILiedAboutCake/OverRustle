var React = require('react');
var classNames = require('classnames');

// tutorial3.js
var StreamBox = React.createClass({displayName: "StreamBox",
  getInitialState: function() {
    // called server-side
    return {strim_list: this.props.strim_list};
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
    var rnkey = Math.random().toString();

    return (
      React.createElement("div", {className: "streamBox"}, 
        React.createElement(StreamList, {key: rnkey, strim_list: this.state.strim_list})
      )
    );
  }
});

var StreamList = React.createClass({displayName: "StreamList",
  // getInitialState: function () {
  //   return {strim_list: this.props.strim_list, disabled: true};
  // },
  render: function() {
    var list = this.props.strim_list || [];
    console.log('rendering stream list', list.length, "long list")
    console.log(list)
    var streamNodes = list.map(function (stream) {
      // config the name/title/label
      if(!stream.metadata){
        stream.metadata = {}
      }
      stream.metadata.label = stream.metadata.channel+" on "+stream.metadata.platform
      if(stream.metadata.hasOwnProperty('name') && stream.metadata.name.length > 0){
        stream.metadata.label = stream.metadata.name+"\'s channel"
      }

      // config the badge/view counter
      var classes = classNames({
        'pull-right label label-as-badge': true,
        'label-success': stream.metadata['live'],
        'label-danger': !stream.metadata['live']
      });

      var visibility = stream.metadata['live'] ? 'visible' : 'hidden' 

      return (
        React.createElement(Stream, {key: stream.strim, metadata: stream.metadata, live_class: classes, visibility: visibility})
      );
    });
    var allNodes = []
    streamNodes.forEach(function(s){
      allNodes.push(s)
      allNodes.push(React.createElement("div", {className: "clear"}))
    })
    console.log(allNodes)
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
