import { TestBed } from '@angular/core/testing';
import { TestServiceService } from './tweets-service.service';

describe('TestServiceService', () => {
  let service: TestServiceService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(TestServiceService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });
});
