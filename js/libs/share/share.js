/* 
 * WebLife CMS
 * Created on 06.08.2018, 17:25:42
 * Developed by http://weblife.ua/
 */

function CShare() {
    var Share = {
        url: null,
        params: {
            toolbar: 0,
            status: 0,
            width: 0,
            height: 0
        },
	vkontakte: function(purl, ptitle, pimg, text) {
            var self = this;
            self.url  = '//vkontakte.ru/share.php?';
            self.url += 'url='          + encodeURIComponent(purl);
            self.url += '&title='       + encodeURIComponent(ptitle);
            self.url += '&description=' + encodeURIComponent(text);
            self.url += '&image='       + encodeURIComponent(pimg);
            self.url += '&noparse=true';
            self.popup();
	},
	odnoklassniki: function(purl, text) {
            var self = this;
            self.url  = '//www.odnoklassniki.ru/dk?st.cmd=addShare&st.s=1';
            self.url += '&st.comments=' + encodeURIComponent(text);
            self.url += '&st._surl='    + encodeURIComponent(purl);
            self.popup();
	},
	facebook: function(purl, ptitle, pimg, text) {
            var self = this;
            self.url  = '//www.facebook.com/sharer.php?s=100';
            self.url += '&p[title]='     + encodeURIComponent(ptitle);
            self.url += '&p[summary]='   + encodeURIComponent(text);
            self.url += '&p[url]='       + encodeURIComponent(purl);
            self.url += '&p[images][0]=' + encodeURIComponent(pimg);
            self.popup();
	},
	twitter: function(purl, ptitle) {
            var self = this;
            self.url  = '//twitter.com/share?';
            self.url += 'text='      + encodeURIComponent(ptitle);
            self.url += '&url='      + encodeURIComponent(purl);
            self.url += '&counturl=' + encodeURIComponent(purl);
            self.popup();
	},
	mailru: function(purl, ptitle, pimg, text) {
            var self = this;
            self.url  = '//connect.mail.ru/share?';
            self.url += 'url='          + encodeURIComponent(purl);
            self.url += '&title='       + encodeURIComponent(ptitle);
            self.url += '&description=' + encodeURIComponent(text);
            self.url += '&imageurl='    + encodeURIComponent(pimg);
            self.popup()
	},
	google: function(purl) {
            var self = this;
            self.url  = '//plus.google.com/share?';
            self.url += 'url=' + encodeURIComponent(purl);
            self.popup()
	},
	popup: function() {
            var self = this;
            window.open(self.url, '', 'toolbar='+self.params.toolbar+',status='+self.params.status+',width='+self.params.width+',height='+self.params.height);
	}
    };
    this.init = function() {
        return Share;
    };
};