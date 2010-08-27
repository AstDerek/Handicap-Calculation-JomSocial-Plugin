if (typeof(Handicap) !== 'object') {
  Handicap = {};
}

Handicap.tweeted = function (plain,html) {
  cWindowShow('jQuery("#cWindowContent").html("<h3>Tweet posted successfully!</h3><h4>'+plain+'</h4>")','Twitter',500,200);
}

jQuery(document).ready(function(){
  if (jax) {
    jax.buildXmlReq = function (comName,func,postData,responseFunc,iframe) {
      var xmlReq = '';
      
      if (iframe) {
        xmlReq += '?';
      }
      else {
        xmlReq += '&';
      }
      
      xmlReq += 'option=' + comName;
      xmlReq += '&no_html=1';
      xmlReq += '&task=azrul_ajax';
      xmlReq += '&func=' + func;
      
      if (Handicap.userid) {
        xmlReq += "&userid=" + Handicap.userid;
      }
      
      if (postData) {
        xmlReq += "&" + postData;
      }
      
      return xmlReq;
    }
  }
  
  jQuery('#hc-stats').click(function(){
    cWindowShow('jax.call("community","plugins,handicap,ajaxHandicap","section=my-stats")', Handicap.displayName+' stats', 600, 500);
    jQuery('#cWindowContent').focus();
    return false;
  });
  
  jQuery('#hc-config').click(function(){
    cWindowShow('jax.call("community","plugins,handicap,ajaxHandicap","section=game-configuration")', 'Game Configuration', 600, 300);
    jQuery('#cWindowContent').focus();
    return false;
  });
  
  jQuery('#hc-round').click(function(){
    cWindowShow('jax.call("community","plugins,handicap,ajaxHandicap","section=add-new-round")', 'Add new round', 600, 450);
    jQuery('#cWindowContent').focus();
    return false;
  });
  
  jQuery('#hc-score').click(function(){
    cWindowShow('jax.call("community","plugins,handicap,ajaxHandicap","section=my-scores")', Handicap.displayName+' scores', 600, 450);
    jQuery('#cWindowContent').focus();
    return false;
  });
  
  jQuery('#hc-courses').click(function(){
    cWindowShow('jax.call("community","plugins,handicap,ajaxHandicap","section=my-courses")', Handicap.displayName+' courses', 450, 300);
    jQuery('#cWindowContent').focus();
    return false;
  });
  
  jQuery('#hc-twitter').click(function(){
    var hc = jQuery('#hc-handicap-rating').text().toFloat();
    var sc = jQuery('#hc-average-score').text().toFloat();
    var name = (Handicap.displayName == 'My') ? 'my' : Handicap.displayName.replace('&#039;',"'");
    
    hc = isNaN(hc) ? 0 : hc;
    sc = isNaN(sc) ? 0 : sc;
    
    cWindowShow('twttr.anywhere(function(T){T("#cWindowContent").tweetBox({width:450,label:"Tell the world about '+name+' stats",defaultContent:"Looking at '+name+' handicap stats. Average score: '+sc+' / Handicap rating: '+hc+' #wegolf #handicap",onTweet:Handicap.tweeted});})', 'Twitter', 500, 200);
    jQuery('#cWindowContent').focus();
    return false;
  });
});

jQuery(window).load(function(){
  if (!jQuery('#handicap-recent-rounds').get(0)) {
    return;
  }
  
  new Dygraph(jQuery('#handicap-recent-rounds').get(0),
    Handicap.base+'?userid='+Handicap.userid+'&option=community&no_html=1&task=azrul_ajax&func=plugins,handicap,ajaxHandicap&arg2=["_d_","section=dygraph"]',
    {yAxisLabelWidth:40,width:500,height:280}
  );
});
