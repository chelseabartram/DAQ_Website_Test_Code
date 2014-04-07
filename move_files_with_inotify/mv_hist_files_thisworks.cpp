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
#include <list>
#include <fstream>
#include <dirent.h>

#define PERM_FILE (S_IRUSR | S_IWUSR | S_IRGRP | S_IROTH)

class inotify_processing
{
public:

  /*default constructor*/
  inotify_processing(){max_size=6;drpc="DRPC_MM_DR_DR_";fepc1="FEPC1_MM_FE_";fepc2="FEPC2_MM_FE_";fepc3="FEPC3_MM_FE_";fepc4="FEPC4_MM_FE_";drpc_min=0;fepc1_min=0;fepc2_min=0;fepc3_min=0;fepc4_min=0;}

  void processHist(std::vector<std::string> event_timing_vec, int max_size)
  {
    //std::cerr << event_timing_vec.size() << std::endl;
    //If all the vectors are full (6 entries)
    if((DR_DRAssembler_START.size()==max_size) && (FE1_RATAssembler_START.size()==max_size) && (FE2_RATAssembler_START.size()==max_size) && (FE3_RATAssembler_START.size()==max_size) && (FE4_RATAssembler_START.size()==max_size))
      {
	for(int i=0;i<max_size;i++)
	  {
	    std::cerr << DR_DRAssembler_START[i] << std::endl;
	    processFiles(DR_DRAssembler_START[i],i);
	  }
	for(int i=0;i<max_size;i++)
	  {
	    std::cerr << FE1_RATAssembler_START[i] << std::endl;
	    processFiles(FE1_RATAssembler_START[i],i);
	  }
	for(int i=0;i<max_size;i++)
	  {
	    std::cerr << FE2_RATAssembler_START[i] << std::endl;
	    processFiles(FE2_RATAssembler_START[i],i);
	  }
	for(int i=0;i<max_size;i++)
	  {
	    std::cerr << FE3_RATAssembler_START[i] << std::endl;
	    processFiles(FE3_RATAssembler_START[i],i);
	  }
	for(int i=0;i<max_size;i++)
	  {
	    std::cerr << FE4_RATAssembler_START[i] << std::endl;
	    processFiles(FE4_RATAssembler_START[i],i);
	    }
      }
  };

  /* Function 'processFiles' moves the 6 relevant root files into another directory */
  
  void processFiles(std::string plot_type, int file_index)
  {
    std::cerr << "hello1" << std::endl;

    std::string original_plot_type;
    std::string new_plot_type;
    std::string path="/home/mcalpha/ROOT_Files_test/";
    original_plot_type.assign(plot_type);
    original_plot_type.insert(0,path);
    std::string newpath = "/home/mcalpha/ROOT_Files_Website_test/";
    new_plot_type.assign(plot_type);
    new_plot_type.insert(0,newpath);

    std::cerr << "hello2" << std::endl;

    int fd1;
    int fd2;
    int status;
    ssize_t read_return;
    ssize_t write_return;

    /* get the size of the file that was moved to that directory */
    struct stat stat_buf;

    status = stat(original_plot_type.c_str(),&stat_buf);

    std::cerr << "hello3" << std::endl;
    //Problem: at run-time it still doesn't know how much space to allocate I think
    char *buffer = new char[stat_buf.st_size];

    std::cerr << "hello4" << std::endl;
    /* open the file */
    fd1 = open(original_plot_type.c_str(), O_RDONLY);
    
    std::cerr << "hello5" << std::endl;
    /*  create file in other directory */
    fd2 = open(new_plot_type.c_str(), O_WRONLY | O_CREAT, PERM_FILE);
    
    std::cerr << "hello6" << std::endl;
    /* read from the file to buffer */
    read_return = read(fd1, buffer, stat_buf.st_size);
    
    std::cerr << "hello7" << std::endl;
    /* write from the buffer to the file in the other directory */
    write_return = write(fd2, buffer, stat_buf.st_size);
    
    std::cerr << "hello8" << std::endl;
    /* remove original file */
    unlink(original_plot_type.c_str());

    std::cerr << "hello9" << std::endl;
    delete[] buffer;

    std::cerr << "hello10" << std::endl;
    /* clear relevant vector and start over */

    if((plot_type.compare(0,14,drpc)==0)&&(file_index==(max_size-1)))
      {
	std::cerr << "drpc histogram vector cleared" << std::endl;
	 DR_DRAssembler_START.clear();
      }
    if((plot_type.compare(0,13,fepc1)==0)&&(file_index==(max_size-1)))
      {
	std::cerr << "fepc1 histogram vector cleared" << std::endl;
	FE1_RATAssembler_START.clear();
      }
    if((plot_type.compare(0,13,fepc2)==0)&&(file_index==(max_size-1)))
      {
	std::cerr << "fepc2 histogram vector cleared" << std::endl;
	FE2_RATAssembler_START.clear();
      }
    if((plot_type.compare(0,13,fepc3)==0)&&(file_index==(max_size-1)))
      {
	std::cerr << "fepc3 histogram vector cleared" << std::endl;
	FE3_RATAssembler_START.clear();
      }
    if((plot_type.compare(0,13,fepc4)==0)&&(file_index==(max_size-1)))
      {
	std::cerr << "fepc2 histogram vector cleared" << std::endl;
	FE4_RATAssembler_START.clear();
      }

  };

  /* Function 'notify' sits on the ROOT_Files_test directory waiting for new files to appear there */

  void notify()
  {
    int fd;
    int wd;
    
    fd=inotify_init();
    if(fd<0)
      perror("inotify_init()");
    else
      printf("%d\n",fd);

    std::string path="/home/mcalpha/ROOT_Files_test";

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

  /* Function 'getTimestamp' strips irrelevant info from the file name to return timestamp */

  int getTimestamp(std::string event_name)
  {
    int timestamp_begin;
    size_t file_end;
    int timestamp;
    file_end=event_name.find_first_of(".root");
    event_name.erase(file_end);
    timestamp_begin=event_name.find_last_of("_");
    event_name.erase(0,timestamp_begin+1);
    timestamp=atoi(event_name.c_str());
    return timestamp;
  };

  /* Function 'addToVector' pushes back the file name onto the appropriate vector */  

  void addToVector(std::string event_name)
  {
    int timestamp;
    timestamp=getTimestamp(event_name);

    getAllFiles();

    if(event_name.compare(0,14,drpc)==0)
      {
	std::cerr << "File starts with 'DRPC' string." << std::endl;
	//Check to make sure you actually have the smallest timestamp
	if(timestamp<=drpc_min)
	  {
	    std::cerr << "The file also has the lowest timestamp for drpc." << std::endl;
	    //Guarantees that all the elements in the vector have the same timestamp
	    if(DR_DRAssembler_START.empty())
	      {
		dr_hist_timestamp = timestamp;
		DR_DRAssembler_START.push_back(event_name);
		processHist(DR_DRAssembler_START,max_size);
	      }
	    else if(timestamp==dr_hist_timestamp)
	      {
		DR_DRAssembler_START.push_back(event_name);
		processHist(DR_DRAssembler_START,max_size);
	      }
	    else
	      {
	      DR_DRAssembler_START.clear();
	      dr_hist_timestamp = timestamp;
	      DR_DRAssembler_START.push_back(event_name);
	      processHist(DR_DRAssembler_START,max_size);
	      }
	  }
      }
    else if(event_name.compare(0,12,fepc1)==0)
      {
	std::cerr << "File starts with 'FEPC1' string." << std::endl;
	//Check to make sure you actually have the smallest timestamp
	if(timestamp<=fepc1_min)
	  {
	    std::cerr << "The file also has the lowest timestamp for fepc1." << std::endl;
	    //Guarantees that all the elements in the vector have the same timestamp
	    if(FE1_RATAssembler_START.empty())
	      {
		fe1_hist_timestamp=timestamp;
		FE1_RATAssembler_START.push_back(event_name);
		processHist(FE1_RATAssembler_START,max_size);
	      }
	    else if(timestamp==fe1_hist_timestamp)
	      {
		FE1_RATAssembler_START.push_back(event_name);
		processHist(FE1_RATAssembler_START,max_size);
	      }
	    else
	      {
		FE1_RATAssembler_START.clear();
		fe1_hist_timestamp = timestamp;
		FE1_RATAssembler_START.push_back(event_name);
		processHist(FE1_RATAssembler_START,max_size);
	      }		
	  }
      }
    else if(event_name.compare(0,12,fepc2)==0)
      {
	std::cerr << "File starts with 'FEPC2' string." << std::endl;
	if(timestamp<=fepc2_min)
	  {
	    std::cerr << "The file also has the lowest timestamp for fepc2." << std::endl;
	    //Guarantees that all the elements in the vector have the same timestamp
	    if(FE2_RATAssembler_START.empty())
	      {
		fe2_hist_timestamp=timestamp;
		FE2_RATAssembler_START.push_back(event_name);
		processHist(FE2_RATAssembler_START,max_size);
	      }
	    else if(timestamp==fe2_hist_timestamp)
	      {
		FE2_RATAssembler_START.push_back(event_name);
		processHist(FE2_RATAssembler_START,max_size);
	      }
	    else
	      {
		FE2_RATAssembler_START.clear();
		fe2_hist_timestamp = timestamp;
		FE2_RATAssembler_START.push_back(event_name);
		processHist(FE2_RATAssembler_START,max_size);
	      }
	  }
      }
    else if(event_name.compare(0,12,fepc3)==0)
      {
	std::cerr << "File starts with 'FEPC3' string." << std::endl;
	if(timestamp<=fepc3_min)
	  {
	    std::cerr << "The file also has the lowest timestamp for fepc3." << std::endl;
	    //Guarantees that all the elements in the vector have the same timestamp
	    if(FE3_RATAssembler_START.empty())
	      {
		fe3_hist_timestamp=timestamp;
		FE3_RATAssembler_START.push_back(event_name);
		processHist(FE3_RATAssembler_START,max_size);
	      }
	    else if(fe3_hist_timestamp==timestamp)
	      {
		FE3_RATAssembler_START.push_back(event_name);
		processHist(FE3_RATAssembler_START,max_size);
	      }
	    else
	      {
		FE3_RATAssembler_START.clear();
		fe3_hist_timestamp = timestamp;
		FE3_RATAssembler_START.push_back(event_name);
		processHist(FE3_RATAssembler_START,max_size);
	      }
	  }
      }
    else if(event_name.compare(0,12,fepc4)==0)
      {
	std::cerr << "File starts with 'FEPC4' string." << std::endl;
	if(timestamp<=fepc4_min)
	  {
	    std::cerr << "The file also has the lowest timestamp for fepc4." << std::endl;
	    //Guarantees that all the elements in the vector have the same timestamp
	    if(FE4_RATAssembler_START.empty())
	      {
		fe4_hist_timestamp=timestamp;
		FE4_RATAssembler_START.push_back(event_name);
		processHist(FE4_RATAssembler_START,max_size);
	      }
	    else if(timestamp==fe4_hist_timestamp)
	      {
		FE4_RATAssembler_START.push_back(event_name);
		processHist(FE4_RATAssembler_START,max_size);
	      }
	    else
	      {
		FE4_RATAssembler_START.clear();
		fe4_hist_timestamp = timestamp;
		FE4_RATAssembler_START.push_back(event_name);
		processHist(FE4_RATAssembler_START,max_size);
	      }	
	  }
      }
  };

  /* Function 'getAllFiles' checks the directory to make sure that there are no files with lower timestamps already existing in the directory */
    
  int getAllFiles()
  {
    int file_timestamp;

    DIR *dir;
    struct dirent *ent;
    dir = opendir("/home/mcalpha/ROOT_Files_test");
    if (dir != NULL) {
      /* print all the files and directories within a directory */
      while ((ent = readdir (dir)) != NULL){
	std::string filename;
	filename.assign(ent->d_name);
	if(filename.compare(0,14,drpc)==0)
	  {
	    std::cerr << "Here is the filename: " << filename << std::endl;
	    file_timestamp=getTimestamp(filename);
	    std::cerr << "And here is its timestamp: " << file_timestamp << std::endl;
	    drpc_files.push_back(file_timestamp);
	  }
	if(filename.compare(0,12,fepc1)==0)
	  {
 	    std::cerr << "Here is the filename: " << filename << std::endl;
	    file_timestamp=getTimestamp(filename);
	    std::cerr << "And here is its timestamp: " << file_timestamp << std::endl;
	    fepc1_files.push_back(file_timestamp);
	  }
	if(filename.compare(0,12,fepc2)==0)
	  {
	    std::cerr << "Here is the filename: " << filename << std::endl;
	    file_timestamp=getTimestamp(filename);
	    std::cerr << "And here is its timestamp: " << file_timestamp << std::endl;
	    fepc2_files.push_back(file_timestamp);
	  }
	if(filename.compare(0,12,fepc3)==0)
	  {
	    std::cerr << "Here is the filename: " << filename << std::endl;
	    file_timestamp=getTimestamp(filename);
	    std::cerr << "And here is its timestamp: " << file_timestamp << std::endl;
	    fepc3_files.push_back(file_timestamp);
	  }
	if(filename.compare(0,12,fepc3)==0)
	  {
	    std::cerr << "Here is the filename: " << filename << std::endl;
	    file_timestamp=getTimestamp(filename);
	    std::cerr << "And here is its timestamp: " << file_timestamp << std::endl;
	    fepc4_files.push_back(file_timestamp);
	  }
      }
	closedir(dir);
    }
    else{
      /*could not open directory*/
      perror("");
      return EXIT_FAILURE;
    }
    
    drpc_files.sort();
    fepc1_files.sort();
    fepc2_files.sort();
    fepc3_files.sort();
    fepc4_files.sort();
    
    std::list<int>::iterator it1;
    std::list<int>::iterator it2;
    std::list<int>::iterator it3;
    std::list<int>::iterator it4;
    std::list<int>::iterator it5;
    
    //The one at the end of the list will be the smallest timestamp
    it1=drpc_files.begin();
    drpc_min=*it1;
    it2=fepc1_files.begin();
    fepc1_min=*it2;
    it3=fepc2_files.begin();
    fepc2_min=*it3;
    it4=fepc3_files.begin();
    fepc3_min=*it4;
    it5=fepc4_files.begin();
    fepc4_min=*it5;

    if(drpc_files.empty()==0)
      std::cerr << "The drpc minimum timestamp is: " << drpc_min << std::endl;
    if(fepc1_files.empty()==0)
      std::cerr << "The fepc1 minimum timestamp is: " << fepc1_min << std::endl;
    if(fepc2_files.empty()==0)
      std::cerr << "The fepc2 minimum timestamp is: " << fepc2_min << std::endl;
    if(fepc3_files.empty()==0)
      std::cerr << "The fepc3 minimum timestamp is: " << fepc3_min << std::endl;
    if(fepc4_files.empty()==0)
      std::cerr << "The fepc4 minimum timestamp is: " << fepc4_min << std::endl;

    return 0;
  };

private:

  /* Size of histogram vector when its contents will be moved to another directory */
  int max_size;

  /* Minimum values for timestamps. Initialized in int main(). Should initialize in constructor probably.*/

  int drpc_min;
  int fepc1_min;
  int fepc2_min;
  int fepc3_min;
  int fepc4_min;

  /* Timestamps which should match for a single histogram vector */    

  int dr_hist_timestamp;
  int fe1_hist_timestamp;
  int fe2_hist_timestamp;
  int fe3_hist_timestamp;
  int fe4_hist_timestamp;

  /* Define strings for beginnings of file names to be matched */

  std::string drpc;
  std::string fepc1;
  std::string fepc2;
  std::string fepc3;
  std::string fepc4;

  /* Histogram vectors to be filled with file names that meet criteria for lowest timestamp and correct naming scheme */
  /* Size will never exceed 6. Maybe I should initialize the size */

  std::vector<std::string> DR_DRAssembler_START;
  std::vector<std::string> FE1_RATAssembler_START;
  std::vector<std::string> FE2_RATAssembler_START;
  std::vector<std::string> FE3_RATAssembler_START;
  std::vector<std::string> FE4_RATAssembler_START;

  /* Linked lists to sort files based on timestamp */

  std::list<int> drpc_files;
  std::list<int> fepc1_files;
  std::list<int> fepc2_files;
  std::list<int> fepc3_files;
  std::list<int> fepc4_files;

};

int main(int argc,char *argv[])
{
  inotify_processing ROOT_hist_process;

  //Needed to initialize the minimum timestamp on existing files in the directory
  ROOT_hist_process.getAllFiles();

  while(1)
    {
      ROOT_hist_process.notify();
    }
  return 0;
}
