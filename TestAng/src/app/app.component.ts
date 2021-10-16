import { Component } from '@angular/core';
import { TestServiceService } from './tweets-service.service';
import { Tweet } from './Tweet';
@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.less']
})
export class AppComponent {
  title = 'TestAng';
  
  constructor(private testService:TestServiceService){}
  
}
