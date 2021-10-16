import { ComponentFixture, TestBed } from '@angular/core/testing';

import { TweetsButtonComponent } from './tweets-button.component';

describe('TweetsButtonComponent', () => {
  let component: TweetsButtonComponent;
  let fixture: ComponentFixture<TweetsButtonComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ TweetsButtonComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(TweetsButtonComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
