FROM centos:7
MAINTAINER Trevor DiMartino <trevor.dimartino@colorado.edu>
LABEL Description="CAP environment created to develop PHP locally." \
    License="MIT" \
    Usage="docker run -d -p [HOST WWW PORT NUMBER]:80 -p [HOST DB PORT NUMBER]:3306 -v [HOST WWW DOCUMENT ROOT]:/var/www/html -v [HOST DB DOCUMENT ROOT]:/var/lib/mysql fauria/lamp" \
    Version="1.0"

RUN yum -y install httpd; \
    yum install -y http://rpms.remirepo.net/enterprise/remi-release-7.rpm; \
    yum install -y yum-utils; \
    yum-config-manager --enable remi-php56; \
    yum install -y php php-mcrypt php-cli php-gd php-curl php-mysql php-ldap php-zip php-fileinfo; \
    yum clean all

RUN rm /etc/httpd/conf/httpd.conf
COPY local_httpd.conf /etc/httpd/conf/httpd.conf

COPY . /var/www/html/

EXPOSE 80

CMD [ "/usr/sbin/httpd","-D","FOREGROUND" ]