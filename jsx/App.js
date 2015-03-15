var React = require('react');
var classNames = require('classnames');

// tutorial3.js
var StreamBox = React.createClass({
  getInitialState: function() {
    // called server-side
    return {strim_list: this.props.strim_list};
  },
  componentDidMount: function () {
    // called on client side?
    // this.setState({disabled: false})
    // it's fine to use jquery here
    $(document).on('strim_list', function (e, strim_list) {
      this.setProps({strim_list: strim_list})
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
      <div className="streamBox">
        <StreamList key={rnkey} strim_list={this.props.strim_list} />
      </div>
    );
  }
});

var StreamList = React.createClass({
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
        <Stream key={stream.strim} metadata={stream.metadata} live_class={classes} visibility={visibility} />
      );
    });
    var allNodes = []
    streamNodes.forEach(function(s){
      allNodes.push(s)
      allNodes.push(<div className="clear"></div>)
    })
    console.log(allNodes)
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
      <div className="stream col-xs-12 col-sm-4 col-md-3 col-lg-2">
        <div className="thumbnail">
          <a href={this.props.metadata.url} className={this.props.visibility}>
            <img className="stream-thumbnail" src={this.props.metadata.image_url} alt={this.props.metadata.label} />
          </a>
          <div className="caption">
            <div>
              <a href={this.props.metadata.url} > {this.props.metadata.label}</a>
              <span className={this.props.live_class}>
                {this.props.metadata.rustlers} <span className="glyphicon glyphicon-user" aria-hidden="true"></span> 
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

// var strim_list = [];

// React.render(
//   <StreamBox strim_list={strim_list} />,
//   document.getElementById('strims')
// );
