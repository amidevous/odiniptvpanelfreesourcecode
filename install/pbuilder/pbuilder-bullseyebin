#!/bin/sh
# script from Jamin W. Collins  BTS: #255165
# name this script 'pbuilder-woody', 'pbuilder-sid', 'pbuilder-sarge', 'pbuilder-experimental' etc.

OPERATION=$1
DISTRIBUTION=`basename $0 | cut -f2 -d '-'`
PROCEED=false
BASE_DIR="$HOME/pbuilder"
case $OPERATION in
   create|update|build|clean|login|execute )
      PROCEED=true
      ;;
esac
if ( $PROCEED == true ) then
   shift 
   sudo pbuilder $OPERATION \
      --basetgz $BASE_DIR/$DISTRIBUTION-base.tgz \
      --distribution $DISTRIBUTION \
      --buildresult $BASE_DIR/result $@
else
   echo "Invalid command..."
   echo "Valid commands are:"
   echo "   create"
   echo "   update"
   echo "   build"
   echo "   clean"
   echo "   login"
   echo "   execute"
   exit 1
fi
