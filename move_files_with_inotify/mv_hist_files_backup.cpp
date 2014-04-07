#include <sys/inotify.h>
#include <errno.h>
#include <stdio.h>
#include <linux/types.h>
#include <vector>
#include <sys/types.h>
#include <fcntl.h>
#include <unistd.h>
#include <sys/stat.h>
#include <string>
#include <string.h>
#include <cstdlib>
#include <iostream>
#define PERM_FILE (S_IRUSR | S_IWUSR | S_IRGRP | S_IROTH)


class inotify_processing
{
public:

  /*default constructor*/
  inotify_processing(){max_size=6;drpc="DRPC_MM_DR_DR_";fepc1="FEPC1_MM_FE_";fepc2="FEPC2_MM_FE_";fepc3="FEPC3_MM_FE_";fepc4="FEPC4_MM_FE_";}

  void processHist(std::vector<std::string> event_timing_vec, int max_size)
  {
    //If all the vectors are full (6 entries)
    if(event_timing_vec.size()==max_size)
      {
	for(int i=0;i<max_size;i++)
	  {
	    std::cerr << event_timing_vec[i] << std::endl;
	    processFiles(event_timing_vec[i]);
	  }
      }
  };
  
  void processFiles(std::string plot_type)
  {
    std::string original_plot_type;
    std::string new_plot_type;
    std::string path="/home/mcalpha/ROOT_Files/";
    original_plot_type.assign(plot_type);
    original_plot_type.insert(0,path);
    std::string newpath = "/home/mcalpha/ROOT_Files_Website/";
    new_plot_type.assign(plot_type);
    new_plot_type.insert(0,newpath);

    int fd1;
    int fd2;
    int status;
    ssize_t read_return;
    ssize_t write_return;

    /* get the size of the file that was moved to that directory */
    struct stat stat_buf;

    status = stat(original_plot_type.c_str(),&stat_buf);

    //Problem: at run-time it still doesn't know how much space to allocate I think
    char *buffer = new char[stat_buf.st_size];

    /* open the file */
    fd1 = open(original_plot_type.c_str(), O_RDONLY);
    
    /*  create file in other directory */
    fd2 = open(new_plot_type.c_str(), O_WRONLY | O_CREAT, PERM_FILE);
    
    /* read from the file to buffer */
    read_return = read(fd1, buffer, stat_buf.st_size);
    
    /* write from the buffer to the file in the other directory */
    write_return = write(fd2, buffer, stat_buf.st_size);
    
    /* remove original file */
    unlink(original_plot_type.c_str());

    delete[] buffer;
  };

  void notify()
  {
    int fd;
    int wd;
    
    fd=inotify_init();
    if(fd<0)
      perror("inotify_init()");
    else
      printf("%d\n",fd);
    std::string path="/home/mcalpha/ROOT_Files";

    /* look for new files being moved into this directory */
    wd = inotify_add_watch(fd,path.c_str(),IN_MOVED_TO);
    if(wd<0)
      perror("inotify_add_watch");

    int safe = 200;
    int read_return1;
    ssize_t read_return2;
    
    std::string event_name;
    
    size_t nbytes = sizeof(struct inotify_event);
    struct inotify_event *ptr;
    ptr = (struct inotify_event *) malloc(sizeof(struct inotify_event)+safe);
    if(ptr==NULL)
      {
    	exit(1);
      }

    read_return1=read(fd,ptr,sizeof(struct inotify_event)+safe);
    int n=2;
    while(read_return1<0)
      {
	/* Resize the memory allocation for the event struct in case the name of the file is too long*/
	printf("Errno: %d %s\n",errno,strerror(errno));
	ptr = (struct inotify_event *) realloc(ptr,sizeof(struct inotify_event)+(n*safe));
	read_return1=read(fd,ptr,sizeof(struct inotify_event)+(n*safe));
	n++;
      }

    event_name.assign(ptr->name);
    addToVector(event_name);
    close(wd);
    close(fd);
    free(ptr);
  };
  
  void addToVector(std::string event_name)
  {

    if(event_name.compare(0,13,drpc))
      {
	DR_DRAssembler_START.push_back(event_name);
	processHist(DR_DRAssembler_START,max_size);
      }
    else if(event_name.compare(0,11,fepc1))
      {
	FE1_RATAssembler_START.push_back(event_name);
	processHist(FE1_RATAssembler_START,max_size);
      }
    else if(event_name.compare(0,11,fepc2))
      {
	FE2_RATAssembler_START.push_back(event_name);
	processHist(FE2_RATAssembler_START,max_size);
      }
    else if(event_name.compare(0,11,fepc3))
      {
	FE3_RATAssembler_START.push_back(event_name);
	processHist(FE3_RATAssembler_START,max_size);
    }
    else if(event_name.compare(0,11,fepc4))
      {
	FE4_RATAssembler_START.push_back(event_name);
	processHist(FE4_RATAssembler_START,max_size);
      }
  };

private:
  int max_size;

  std::string drpc;
  std::string fepc1;
  std::string fepc2;
  std::string fepc3;
  std::string fepc4;

  std::vector<std::string> DR_DRAssembler_START;
  std::vector<std::string> FE1_RATAssembler_START;
  std::vector<std::string> FE2_RATAssembler_START;
  std::vector<std::string> FE3_RATAssembler_START;
  std::vector<std::string> FE4_RATAssembler_START;

};

int main()
{
  inotify_processing ROOT_hist_process;

  while(1)
    {
      ROOT_hist_process.notify();
    }
  return 0;
}
