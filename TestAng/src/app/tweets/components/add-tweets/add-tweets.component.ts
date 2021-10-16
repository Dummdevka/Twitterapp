import { Component, OnInit, Output, EventEmitter, ViewChild, ElementRef, Renderer2 } from '@angular/core';
import { Tweet } from 'src/app/Tweet';
import{faPaperPlane} from '@fortawesome/free-regular-svg-icons'
import { faImages } from '@fortawesome/free-regular-svg-icons';
import { faFolderMinus, faEraser } from '@fortawesome/free-solid-svg-icons';
@Component({
  selector: 'app-add-tweets',
  templateUrl: './add-tweets.component.html',
  styleUrls: ['./add-tweets.component.less']
})
export class AddTweetsComponent implements OnInit {
  text!:string;
  username!:string;
  selectedFiles!: FileList;
  imageUploaded = false;
  imageName!: string;
  error = '';
  @Output() onAddTweet = new EventEmitter;
  @Output() onSaveUploaded = new EventEmitter;
  faPaperPlane = faPaperPlane;
  faImages = faImages;
  faFolderMinus = faFolderMinus;
  faEraser = faEraser;

  @ViewChild('takeInput', {static: false}) takeInput!:ElementRef;
  constructor( rd: Renderer2) {
    
   }

  ngOnInit(): void {

  }
  loadFile(event: any){
    this.selectedFiles = event.target.files;
    if(this.selectedFiles && this.selectedFiles.length > 0){
      if(this.selectedFiles[0].type.match('.( jpg|jpeg|png )')){
        const imageName = this.selectedFiles[0];
      //Save the image 
      //this.onSaveUploaded.emit(imageName);
      //Show that it's added
      this.imageUploaded = true;
      this.imageName = imageName.name;
      } else{
        this.setError("Invalid format :(");
      }
      
    }
  }
  AddTweet(){
    let image = null;
    //Validation
  if(!this.text){
    this.setError("You forgot something :(");
    return;
  }
  if(this.selectedFiles){
   image = this.selectedFiles[0];
  }
  
  //Creating a new Tweet
  const tweet: Tweet ={
    tweet: this.text,
    image: image
  }

  //Call the service
 this.onAddTweet.emit(tweet);  
  // //Clear fields
  this.text = '';
  }
  
  clearImage(){
    
    this.takeInput.nativeElement.value = '';
    this.imageName = this.takeInput.nativeElement.value ;
    this.imageUploaded = false;
    console.log(    this.takeInput.nativeElement.value 
    )

  }
  setError(error: string){
    this.error = error;

    setTimeout(()=>{
      this.error = '';
    }, 1800);
  }
}

