var React = require('react');
global.React = React;
// var MagicMove = require('./react-magic-move');
var classNames = require('classnames');

var process_api = function(api_data) {
  // console.log('processing api data ', api_data)
  var strims = api_data["streams"]

  var stream_list = []

  var shownsfw = false

  if(typeof localStorage != 'undefined'){
    shownsfw = localStorage.getItem("shownsfw")=="true"
  }
  console.log("SHOWING NSFW?", shownsfw)

  for ( var strim in strims ) {
    if ( Object.prototype.hasOwnProperty.call(strims, strim) ) {
      if (strim.toLowerCase().indexOf('nsfw') !== -1) {
        if(shownsfw){
          stream_list.push(api_data.metadata[api_data.metaindex[strim]])
        }
      }else{
        stream_list.push(api_data.metadata[api_data.metaindex[strim]])
      }
    }
  }
  // console.log(stream_list.length, ' long stream list, ex:', stream_list[0])
  stream_list.sort(function(a,b) {
    // give LIVE streams more weight in sorting higher
    var amulti = a.hasOwnProperty('live') && a['live'] ? 1000 : 1 ;
    var bmulti = b.hasOwnProperty('live') && b['live'] ? 1000 : 1 ;
    if (amulti*a.rustlers < bmulti*b.rustlers)
       return 1;
    if (amulti*a.rustlers > bmulti*b.rustlers)
      return -1;
    return 0;
  })
  return stream_list
}
// tutorial3.js
var StreamBox = React.createClass({
  getInitialState: function() {
    // called server-side
    return {stream_list: process_api(this.props.api_data)};
  },
  componentDidMount: function () {
    // called on client side?
    // this.setState({disabled: false})
    // it's fine to use jquery here

    $(document).on('api_data', function (e, api_data) {
      console.log('new api data', api_data)
      var npl = process_api(api_data)
      console.log('new processed api data', npl)
      this.setState({stream_list: npl})
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
    // console.log(this.state.stream_list.length, ' rendering that long list', this.state.stream_list[0])
    return (
      <div className="streamBox">
        <StreamList key="stream-list" stream_list={this.state.stream_list} />
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
      stream.label = stream.channel+" on "+stream.platform
      if(stream.hasOwnProperty('name') && stream.name.length > 0){
        stream.label = stream.name+"\'s channel"
      }

      // config the badge/view counter
      var classes = classNames({
        'pull-right label label-as-badge': true,
        'label-success': stream['live'],
        'label-danger': !stream['live']
      });

      allNodes.push(
        <Stream key={stream.url} stream={stream} live_class={classes} />
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
          <a href={this.props.stream.url}>
            <img className="stream-thumbnail" src={this.props.stream.image_url} alt={this.props.stream.label} />
          </a>
          <div className="caption">
            <div>
              <a href={this.props.stream.url} > {this.props.stream.label}</a>
              <span className={this.props.live_class}>
                {this.props.stream.rustlers} <span className="glyphicon glyphicon-user" aria-hidden="true"></span> 
              </span>
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
