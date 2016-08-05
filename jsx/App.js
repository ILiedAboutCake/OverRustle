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
  console.log("SHOWING NSFW?", shownsfw)
  if(typeof document != 'undefined' && document['title']){
    console.log('setting title')
    document.title = stream_list.length + " Live Streams viewed by " + api_data.viewercount + " rustlers - OverRustle"
  }

  if(!shownsfw){
    // filter out NSFW streams if needed
    stream_list = stream_list.filter(function(stream){
      return stream['platform'].toLowerCase().indexOf('nsfw') === -1 && stream['channel'].toLowerCase().indexOf('nsfw') === -1;
    })
  }
  return stream_list
}
// tutorial3.js
var StreamBox = React.createClass({
  getInitialState: function() {
    var npl = process_api(this.props.api_data)
    var new_props = {}
    // called server-side
    new_props.featured_stream_list = npl.filter(function (stream){
      return stream['live'] && stream['featured']
    })
    new_props.live_rustler_list = npl.filter(function (stream){
      return stream['live'] && stream.hasOwnProperty('name') && stream.name.length > 0
    })
    new_props.live_stream_list = npl.filter(function (stream) {
      return stream['live'] && !stream['featured'] && (!stream.hasOwnProperty('name') || stream.name.length == 0)
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
        featured_stream_list: npl.filter(function (stream) {
          return stream['live'] && stream['featured']
        }),
        live_rustler_list: npl.filter(function (stream){
          return stream['live'] && stream.hasOwnProperty('name') && stream.name.length > 0
        }),
        live_stream_list: npl.filter(function (stream) {
          return stream['live'] && !stream['featured'] && (!stream.hasOwnProperty('name') || stream.name.length == 0)
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
    var featured_parts = []

    if(this.state.featured_stream_list.length > 0){
      featured_parts.push(<h3>Featured Streams</h3>)
      featured_parts.push(<StreamList key="featured-stream-list" stream_list={this.state.featured_stream_list} />)
    }

    var live_rustler_parts = []

    if(this.state.live_rustler_list.length > 0){
      live_rustler_parts.push(<h3>Live OverRustle Streamers</h3>)
      live_rustler_parts.push(<StreamList key="live-rustler-list" stream_list={this.state.live_rustler_list} />)
    }
    return (
      <div className="streamBox">
        {featured_parts}
        {live_rustler_parts}
        <h3>Live Streams</h3>
        <StreamList key="live-stream-list" stream_list={this.state.live_stream_list} />
        <h3>Offline Streams</h3>
        <StreamList key="offline-stream-list" stream_list={this.state.offline_stream_list} />
      </div>
    );
  }
});

var StreamList = React.createClass({
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
      // TODO: consider moving this to the API server
      stream.url = stream['canonical_url'] ? stream['canonical_url'] : stream.url

      allNodes.push(
        <Stream key={stream.url} stream={stream} shown_thumb={shown_thumb} live_class={classes} />
      );
      i = i + 1;
      var clearkey = "clear-"+stream.url;

      allNodes.push(<div key={clearkey} className="clear"></div>)
    });
    // console.log(allNodes)
    // change these divs to MagicMove elements when we figure that out
    return (
      <div className="streamList row stream-list">
        {allNodes}
      </div>
    );
  }
});

// markdown processor

var Stream = React.createClass({
  render: function() {
    return (
      <div className="sortableStream stream col-xs-12 col-sm-4 col-md-3 col-lg-2">
        <div className="thumbnail">
          <a href={this.props.stream.url} className={this.props.shown_thumb}>
            <img className="stream-thumbnail" src={this.props.stream.image_url} alt={this.props.stream.label} />
          </a>
          <div className="caption">
            <div>
              <div className="stream-label">
                <a href={this.props.stream.url}> 
                  <div>{this.props.stream.label}
                    <span className={this.props.live_class}>
                      {this.props.stream.rustlers} <span className="glyphicon glyphicon-user" aria-hidden="true"></span> 
                    </span>
                  </div>
                  <div className="stream-sublabel">{this.props.stream.sublabel}</div>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
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
