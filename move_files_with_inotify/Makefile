LANG=CPP

CXX= g++
LD= g++
CXXFLAGS  += -g -O2
LDFLAGS   += -g -O2

#------------------------------------------------------------------------------

MV_HIST_FILES	= mv_files
INOTIFY_PROCESS = inotify_process
MV_HIST_FILESO	= $(MV_HIST_FILES).o
INOTIFY_PROCESSO = $(INOTIFY_PROCESS).o
MV_HIST_FILESS	= $(MV_HIST_FILES).cpp
INOTIFY_PROCESSS = $(INOTIFY_PROCESS).cpp
INOTIFY_PROCESSH = $(INOTIFY_PROCESS).h

OBJS          = $(MV_HIST_FILESO) $(INOTIFY_PROCESSO)
PROGRAMS      = $(MV_HIST_FILES)

#------------------------------------------------------------------------------

all:            $(PROGRAMS)

$(MV_HIST_FILES):	$(MV_HIST_FILESO) $(INOTIFY_PROCESSO)
	$(LD) $(LDFLAGS) $(MV_HIST_FILESO) $(INOTIFY_PROCESSO) -o $(MV_HIST_FILES)

$(MV_HIST_FILESO):	$(INOTIFY_PROCESSH) $(MV_HIST_FILESS) 
	$(LD) $(LDFLAGS) -c $(MV_HIST_FILESS)

$(INOTIFY_PROCESSO):	$(INOTIFY_PROCESSH) $(INOTIFY_PROCESSS)
	$(LD) $(LDFLAGS) -c $(INOTIFY_PROCESSS)

clean:
		@rm -f $(OBJS)

distclean:      clean
		@rm -f $(PROGRAMS)
