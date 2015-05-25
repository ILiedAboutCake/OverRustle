var React = require('react');
global.React = React;
// var MagicMove = require('./react-magic-move');
var classNames = require('classnames');

var process_api = function(api_data) {
  // console.log('processing api data ', api_data)
  var stream_list = api_data["stream_list"]

  var shownsfw = false

  if(typeof localStorage != 'undefined'){
    shownsfw = localStorage.getItem("shownsfw")=="true"
  }
  // console.log("SHOWING NSFW?", shownsfw)
  if(typeof document != 'undefined' && document.hasOwnProperty('title')){
    document.title = Object.keys(strims).length + " Live Streams viewed by " + api_data.viewercount + " rustlers - OverRustle"
  }

  if(!shownsfw){
    // filter out NSFW streams if needed
    stream_list = stream_list.filter(function(stream){
      return stream['platform'].toLowerCase().indexOf('nsfw') === -1
    })
  }
  return stream_list
}
// tutorial3.js
var StreamBox = React.createClass({displayName: "StreamBox",
  getInitialState: function() {
    var npl = process_api(this.props.api_data)
    var new_props = {}
    // called server-side
    new_props.live_stream_list = npl.filter(function (stream) {
      return stream['live']
    })
    new_props.offline_stream_list = npl.filter(function (stream) {
      return !stream['live']
    })
    return new_props;
  },
  componentDidMount: function () {
    // called on client side?
    // this.setState({disabled: false})
    // it's fine to use jquery here

    $(document).on('api_data', function (e, api_data) {
      console.log('new api data', api_data)
      var npl = process_api(api_data)
      console.log('new processed api data', npl)
      var new_state = {
        live_stream_list: npl.filter(function (stream) {
          return stream['live']
        }),
        offline_stream_list: npl.filter(function (stream) {
          return !stream['live']
        })
      }
      this.setState(new_state)
    }.bind(this));
  },
  render: function() {
    // console.log(this.state.stream_list.length, ' rendering that long list', this.state.stream_list[0])
    return (
      React.createElement("div", {className: "streamBox"}, 
        React.createElement("h3", null, "Live Streams"), 
        React.createElement(StreamList, {key: "live-stream-list", stream_list: this.state.live_stream_list}), 
        React.createElement("h3", null, "Offline Streams"), 
        React.createElement(StreamList, {key: "offline-stream-list", stream_list: this.state.offline_stream_list})
      )
    );
  }
});

var StreamList = React.createClass({displayName: "StreamList",
  render: function() {
    var list = this.props.stream_list || [];
    // console.log('rendering stream list', list.length, "long list")
    // console.log(list)

    var allNodes = []
    var i = 0;
    list.forEach(function (stream) {
      // config the name/title/label
      if(!stream){
        stream = {}
      }

      if(stream.hasOwnProperty('name') && stream.name.length > 0){
        stream.label = stream.name
        stream.sublabel = "via " + stream.channel + " on " + stream.platform
      }else{
        stream.label = stream.channel
        stream.sublabel = "on "+stream.platform
      }

      // config the badge/view counter
      var classes = classNames({
        'pull-right label label-as-badge': true,
        'label-success': stream['live'],
        'label-danger': !stream['live']
      });

      var shown_thumb = stream.live ? "" : "hidden"

      allNodes.push(
        React.createElement(Stream, {key: stream.url, stream: stream, shown_thumb: shown_thumb, live_class: classes})
      );
      i = i + 1;
      var clearkey = "clear-"+stream.url;

      allNodes.push(React.createElement("div", {key: clearkey, className: "clear"}))
    });
    // console.log(allNodes)
    // change these divs to MagicMove elements when we figure that out
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
      React.createElement("div", {className: "sortableStream stream col-xs-12 col-sm-4 col-md-3 col-lg-2"}, 
        React.createElement("div", {className: "thumbnail"}, 
          React.createElement("a", {href: this.props.stream.url, className: this.props.shown_thumb}, 
            React.createElement("img", {className: "stream-thumbnail", src: this.props.stream.image_url, alt: this.props.stream.label})
          ), 
          React.createElement("div", {className: "caption"}, 
            React.createElement("div", null, 
              React.createElement("div", {className: "stream-label"}, 
                React.createElement("a", {href: this.props.stream.url}, 
                  React.createElement("div", null, this.props.stream.label, 
                    React.createElement("span", {className: this.props.live_class}, 
                      this.props.stream.rustlers, " ", React.createElement("span", {className: "glyphicon glyphicon-user", "aria-hidden": "true"})
                    )
                  ), 
                  React.createElement("div", {className: "stream-sublabel"}, this.props.stream.sublabel)
                )
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

// var stream_list = [];

// React.render(
//   <StreamBox stream_list={stream_list} />,
//   document.getElementById('strims')
// );
