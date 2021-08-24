import { ComponentFixture, TestBed } from '@angular/core/testing';

import { AddTweetsComponent } from './add-tweets.component';

describe('AddTweetsComponent', () => {
  let component: AddTweetsComponent;
  let fixture: ComponentFixture<AddTweetsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ AddTweetsComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(AddTweetsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
