FROM centos:7
MAINTAINER Trevor DiMartino <trevor.dimartino@colorado.edu>
LABEL Description="CAP environment created to develop PHP locally." \
    License="MIT" \
    Usage="docker run -d -p [HOST WWW PORT NUMBER]:80 -p [HOST DB PORT NUMBER]:3306 -v [HOST WWW DOCUMENT ROOT]:/var/www/html -v [HOST DB DOCUMENT ROOT]:/var/lib/mysql fauria/lamp" \
    Version="1.0"

RUN yum install -y http://rpms.remirepo.net/enterprise/remi-release-7.rpm
RUN yum install -y yum-utils
RUN yum-config-manager --enable remi-php56
RUN yum install -y php php-mcrypt php-cli php-gd php-curl php-mysql php-ldap php-zip php-fileinfo

COPY . /var/www/html/

EXPOSE 80
EXPOSE 443

CMD ["httpd"]